use tokio::process::Command;

#[derive(Debug, Clone, Default)]
pub struct ProtocolStatus {
    pub wireguard_installed: bool,
    pub openvpn_installed: bool,
    pub ikev2_installed: bool, // strongSwan
    pub l2tp_installed: bool,
}

async fn cmd_exists(bin: &str) -> bool {
    Command::new("/bin/sh").arg("-lc").arg(format!("command -v {} >/dev/null 2>&1", bin)).status().await.map(|s| s.success()).unwrap_or(false)
}

async fn service_exists(name: &str) -> bool {
    Command::new("/bin/sh").arg("-lc").arg(format!("systemctl list-unit-files | grep -q {}", name)).status().await.map(|s| s.success()).unwrap_or(false)
}

impl ProtocolStatus {
    pub async fn detect() -> Self {
        let (wg_bin, openvpn_bin, swan_bin, l2tp_bin) = tokio::join!(
            cmd_exists("wg"),
            cmd_exists("openvpn"),
            cmd_exists("strongswan"),
            cmd_exists("xl2tpd"),
        );

        // Also try service check fallbacks
        let (wg_svc, openvpn_svc, swan_svc, l2tp_svc) = tokio::join!(
            service_exists("wg-quick"),
            service_exists("openvpn"),
            service_exists("strongswan"),
            service_exists("xl2tpd"),
        );

        Self {
            wireguard_installed: wg_bin || wg_svc,
            openvpn_installed: openvpn_bin || openvpn_svc,
            ikev2_installed: swan_bin || swan_svc,
            l2tp_installed: l2tp_bin || l2tp_svc,
        }
    }
}
