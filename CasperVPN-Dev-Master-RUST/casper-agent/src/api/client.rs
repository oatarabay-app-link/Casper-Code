use anyhow::{Context, Result};
use reqwest::Client;

use super::models::{ServerCreate, ServerStatusUpdate, ServerSettings};

#[derive(Clone)]
pub struct ApiClient {
    base: String,
    token: Option<String>,
    http: Client,
}

impl ApiClient {
    pub fn new(base: String, token: Option<String>) -> Self {
        let http = Client::builder()
            .user_agent("casper-agent/0.1")
            .build()
            .expect("reqwest client");
        Self { base, token, http }
    }

    pub async fn create_server(&self, payload: &ServerCreate) -> Result<()> {
        let url = format!("{}/Server/create", self.trimmed_base());
        let mut req = self.http.post(url).json(payload);
        if let Some(t) = &self.token {
            req = req.bearer_auth(t);
        }
        let resp = req.send().await.context("send create_server request")?;
        if !resp.status().is_success() {
            let status = resp.status();
            let body = resp.text().await.unwrap_or_default();
            anyhow::bail!("API error: {} - {}", status, body);
        }
        Ok(())
    }

    pub async fn update_server_status(&self, payload: &ServerStatusUpdate) -> Result<()> {
        let url = format!("{}/Server/status", self.trimmed_base());
        let mut req = self.http.put(url).json(payload);
        if let Some(t) = &self.token {
            req = req.bearer_auth(t);
        }
        let resp = req.send().await.context("send update_server_status request")?;
        if !resp.status().is_success() {
            let status = resp.status();
            let body = resp.text().await.unwrap_or_default();
            anyhow::bail!("API error: {} - {}", status, body);
        }
        Ok(())
    }

    pub async fn get_server_settings(&self, server_name: &str) -> Result<ServerSettings> {
        let url = format!("{}/Server/settings/{}", self.trimmed_base(), server_name);
        let mut req = self.http.get(url);
        if let Some(t) = &self.token {
            req = req.bearer_auth(t);
        }
        let resp = req.send().await.context("send get_server_settings request")?;
        if !resp.status().is_success() {
            let status = resp.status();
            let body = resp.text().await.unwrap_or_default();
            anyhow::bail!("API error: {} - {}", status, body);
        }
        let settings = resp.json::<ServerSettings>().await.context("parse server settings")?;
        Ok(settings)
    }

    fn trimmed_base(&self) -> String {
        let b = self.base.trim_end_matches('/');
        if b.ends_with("/api") { b.to_string() } else { format!("{}/api", b) }
    }
}
