use serde::{Deserialize, Serialize};

// Request shape: default ASP.NET JSON is camelCase and enums are numeric unless configured otherwise.
#[derive(Debug, Clone, Serialize, Deserialize)]
#[serde(rename_all = "camelCase")]
pub struct ServerCreate {
    pub server_name: String,
    pub connection_protocol: i32,
    pub location: String,
    pub server_status: i32,
    pub max_users: i32,
    pub connection_timeout: i32,
    pub health_check_interval: i32,
    pub ip_address: String,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
#[serde(rename_all = "camelCase")]
pub struct ServerStatusUpdate {
    pub server_name: String,
    pub server_status: i32,
    pub connected_users: i32,
    pub load: i32,
    pub cpu_usage: f32,
    pub memory_usage: f32,
    pub disk_usage: f32,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
#[serde(rename_all = "camelCase")]
pub struct ServerSettings {
    pub max_users: i32,
    pub connection_timeout: i32,
    pub health_check_interval: i32,
    pub server_status: i32,
    pub auto_provision: bool,
}

#[repr(i32)]
#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum ConnectionProtocol {
    OpenVPN = 0,
    WireGuard = 1,
    IKEv2 = 2,
}

#[repr(i32)]
#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum ServerStatus {
    Online = 0,
    Maintenance = 1,
    Offline = 2,
}
