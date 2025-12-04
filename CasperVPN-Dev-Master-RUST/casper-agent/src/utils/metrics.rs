use anyhow::Result;
use serde::{Deserialize, Serialize};
use std::fs;

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct SystemMetrics {
    pub cpu_usage: f32,
    pub memory_usage: f32,
    pub disk_usage: f32,
    pub connected_users: i32,
    pub load: i32,
}

impl SystemMetrics {
    pub async fn collect() -> Result<Self> {
        let cpu = Self::get_cpu_usage().await?;
        let memory = Self::get_memory_usage()?;
        let disk = Self::get_disk_usage()?;
        let connected_users = Self::get_connected_users().await?;
        let load = Self::calculate_load(cpu, memory, connected_users);

        Ok(Self {
            cpu_usage: cpu,
            memory_usage: memory,
            disk_usage: disk,
            connected_users,
            load,
        })
    }

    async fn get_cpu_usage() -> Result<f32> {
        // Read /proc/stat for CPU usage
        #[cfg(target_os = "linux")]
        {
            let stat1 = Self::read_cpu_stat()?;
            tokio::time::sleep(tokio::time::Duration::from_millis(100)).await;
            let stat2 = Self::read_cpu_stat()?;

            let total_delta = stat2.total - stat1.total;
            let idle_delta = stat2.idle - stat1.idle;

            if total_delta == 0 {
                return Ok(0.0);
            }

            let usage = 100.0 * (1.0 - (idle_delta as f32 / total_delta as f32));
            Ok(usage.max(0.0).min(100.0))
        }

        #[cfg(not(target_os = "linux"))]
        {
            // Fallback for non-Linux systems
            Ok(0.0)
        }
    }

    #[cfg(target_os = "linux")]
    fn read_cpu_stat() -> Result<CpuStat> {
        let contents = fs::read_to_string("/proc/stat")?;
        let line = contents.lines().next().unwrap_or("");
        let parts: Vec<&str> = line.split_whitespace().collect();

        if parts.len() < 5 || parts[0] != "cpu" {
            anyhow::bail!("Invalid /proc/stat format");
        }

        let user: u64 = parts[1].parse()?;
        let nice: u64 = parts[2].parse()?;
        let system: u64 = parts[3].parse()?;
        let idle: u64 = parts[4].parse()?;

        let total = user + nice + system + idle;
        Ok(CpuStat { total, idle })
    }

    fn get_memory_usage() -> Result<f32> {
        #[cfg(target_os = "linux")]
        {
            let contents = fs::read_to_string("/proc/meminfo")?;
            let mut total_kb = 0u64;
            let mut available_kb = 0u64;

            for line in contents.lines() {
                if line.starts_with("MemTotal:") {
                    total_kb = line.split_whitespace().nth(1).unwrap_or("0").parse()?;
                } else if line.starts_with("MemAvailable:") {
                    available_kb = line.split_whitespace().nth(1).unwrap_or("0").parse()?;
                }
            }

            if total_kb == 0 {
                return Ok(0.0);
            }

            let used = total_kb - available_kb;
            Ok((used as f32 / total_kb as f32) * 100.0)
        }

        #[cfg(not(target_os = "linux"))]
        {
            Ok(0.0)
        }
    }

    fn get_disk_usage() -> Result<f32> {
        #[cfg(target_os = "linux")]
        {
            // Use statvfs to get disk usage for root partition
            use std::ffi::CString;
            let path = CString::new("/").unwrap();
            
            // Simplified approach: read from df command output
            use std::process::Command;
            let output = Command::new("df")
                .arg("-h")
                .arg("/")
                .output()?;

            if output.status.success() {
                let stdout = String::from_utf8_lossy(&output.stdout);
                if let Some(line) = stdout.lines().nth(1) {
                    let parts: Vec<&str> = line.split_whitespace().collect();
                    if parts.len() >= 5 {
                        let usage_str = parts[4].trim_end_matches('%');
                        if let Ok(usage) = usage_str.parse::<f32>() {
                            return Ok(usage);
                        }
                    }
                }
            }
            Ok(0.0)
        }

        #[cfg(not(target_os = "linux"))]
        {
            Ok(0.0)
        }
    }

    async fn get_connected_users() -> Result<i32> {
        #[cfg(target_os = "linux")]
        {
            // Count active VPN connections
            // This is a placeholder - actual implementation would check WireGuard, OpenVPN, etc.
            // For WireGuard: wg show all dump | grep -c "^[[:space:]]"
            // For OpenVPN: check status files or management interface
            
            use tokio::process::Command;
            
            // Try WireGuard first
            let wg_output = Command::new("wg")
                .arg("show")
                .arg("all")
                .arg("dump")
                .output()
                .await;

            if let Ok(output) = wg_output {
                if output.status.success() {
                    let stdout = String::from_utf8_lossy(&output.stdout);
                    let count = stdout.lines()
                        .filter(|line| line.starts_with('\t') || line.starts_with(' '))
                        .count();
                    return Ok(count as i32);
                }
            }

            Ok(0)
        }

        #[cfg(not(target_os = "linux"))]
        {
            Ok(0)
        }
    }

    fn calculate_load(cpu: f32, memory: f32, connected_users: i32) -> i32 {
        // Calculate load as a percentage (0-100)
        // Weighted average: 40% CPU, 40% memory, 20% user ratio
        let cpu_weight = cpu * 0.4;
        let memory_weight = memory * 0.4;
        // Assume max 200 users for normalization
        let user_ratio = (connected_users as f32 / 200.0).min(1.0) * 100.0;
        let user_weight = user_ratio * 0.2;

        (cpu_weight + memory_weight + user_weight) as i32
    }
}

#[cfg(target_os = "linux")]
struct CpuStat {
    total: u64,
    idle: u64,
}
