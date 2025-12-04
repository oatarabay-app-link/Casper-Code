use anyhow::{bail, Context, Result};

#[derive(Clone, Debug)]
pub struct Config {
    pub api_base: String,
    pub api_token: Option<String>,
    pub auto_provision: bool,
    pub location: Option<String>,
    pub max_users: i32,
    pub connection_timeout_secs: u64,
    pub health_check_interval_secs: u64,
}

impl Config {
    pub fn from_env() -> Result<Self> {
        let api_base = std::env::var("API_BASE_URL")
            .context("Missing API_BASE_URL env var, e.g. https://example.com/api")?;

        // Simple validation: must start with http
        if !(api_base.starts_with("http://") || api_base.starts_with("https://")) {
            bail!("API_BASE_URL must start with http:// or https://");
        }

        let api_token = std::env::var("API_TOKEN").ok();
        let auto_provision = std::env::var("AUTO_PROVISION")
            .map(|v| matches!(v.to_ascii_lowercase().as_str(), "1" | "true" | "yes"))
            .unwrap_or(true);

        let location = std::env::var("SERVER_LOCATION").ok();
        let max_users = std::env::var("MAX_USERS").ok().and_then(|v| v.parse().ok()).unwrap_or(200);
        let connection_timeout_secs = std::env::var("CONNECTION_TIMEOUT_SECS").ok().and_then(|v| v.parse().ok()).unwrap_or(30);
        let health_check_interval_secs = std::env::var("HEALTH_CHECK_INTERVAL_SECS").ok().and_then(|v| v.parse().ok()).unwrap_or(60);

        Ok(Self {
            api_base,
            api_token,
            auto_provision,
            location,
            max_users,
            connection_timeout_secs,
            health_check_interval_secs,
        })
    }
}
