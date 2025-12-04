mod utils;
mod checks;
mod provision;
mod api;

use anyhow::Result;
use tokio::time::{interval, Duration};

#[tokio::main]
async fn main() -> Result<()> {
    // Load .env if present
    let _ = dotenvy::dotenv();

    let cfg = utils::config::Config::from_env()?;

    // Detect server info
    let hostname = utils::system::hostname();
    let ip = utils::system::primary_ipv4().unwrap_or_else(|| "0.0.0.0".into());

    // Check protocol availability
    let proto_status = checks::protocols::ProtocolStatus::detect().await;

    // Optionally provision missing components (no-op stubs for now)
    if cfg.auto_provision {
        if !proto_status.wireguard_installed {
            if let Err(e) = provision::wireguard::install().await {
                eprintln!("WireGuard install failed: {e}");
            }
        }
        if !proto_status.openvpn_installed {
            if let Err(e) = provision::openvpn::install().await {
                eprintln!("OpenVPN install failed: {e}");
            }
        }
        if !proto_status.ikev2_installed {
            if let Err(e) = provision::strongswan::install().await {
                eprintln!("IKEv2 (strongSwan) install failed: {e}");
            }
        }
        if !proto_status.l2tp_installed {
            if let Err(e) = provision::l2tp::install().await {
                eprintln!("L2TP install failed: {e}");
            }
        }
    }

    // Build payload and send to API (create or update server)
    let client = api::client::ApiClient::new(cfg.api_base.clone(), cfg.api_token.clone());

    let payload = api::models::ServerCreate {
        server_name: hostname.clone(),
        connection_protocol: api::models::ConnectionProtocol::WireGuard as i32, // default; can be made dynamic later
        location: cfg.location.clone().unwrap_or_else(|| "unknown".into()),
        server_status: api::models::ServerStatus::Online as i32,
        max_users: cfg.max_users,
        connection_timeout: cfg.connection_timeout_secs as i32,
        health_check_interval: cfg.health_check_interval_secs as i32,
        ip_address: ip.clone(),
    };

    match client.create_server(&payload).await {
        Ok(_) => println!("Server registered: {hostname} ({ip})"),
        Err(err) => eprintln!("Failed to register server: {err}"),
    }

    // Start periodic status update loop
    println!("Starting periodic status updates every {} seconds...", cfg.health_check_interval_secs);
    let mut ticker = interval(Duration::from_secs(cfg.health_check_interval_secs));
    
    loop {
        ticker.tick().await;
        
        // Collect system metrics
        match utils::metrics::SystemMetrics::collect().await {
            Ok(metrics) => {
                let status_update = api::models::ServerStatusUpdate {
                    server_name: hostname.clone(),
                    server_status: api::models::ServerStatus::Online as i32,
                    connected_users: metrics.connected_users,
                    load: metrics.load,
                    cpu_usage: metrics.cpu_usage,
                    memory_usage: metrics.memory_usage,
                    disk_usage: metrics.disk_usage,
                };

                // Send status update to server
                if let Err(e) = client.update_server_status(&status_update).await {
                    eprintln!("Failed to send status update: {e}");
                } else {
                    println!("Status update sent - Load: {}%, CPU: {:.1}%, Mem: {:.1}%, Users: {}", 
                        metrics.load, metrics.cpu_usage, metrics.memory_usage, metrics.connected_users);
                }

                // Fetch updated settings from server
                match client.get_server_settings(&hostname).await {
                    Ok(settings) => {
                        println!("Fetched settings - MaxUsers: {}, ConnTimeout: {}s, HealthCheck: {}s", 
                            settings.max_users, settings.connection_timeout, settings.health_check_interval);
                        // TODO: Apply settings dynamically (would require Arc<Mutex<Config>> or similar)
                    }
                    Err(e) => {
                        eprintln!("Failed to fetch settings: {e}");
                    }
                }
            }
            Err(e) => {
                eprintln!("Failed to collect metrics: {e}");
            }
        }
    }
}
