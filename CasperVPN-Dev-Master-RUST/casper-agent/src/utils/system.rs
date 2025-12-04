use get_if_addrs::get_if_addrs;

pub fn hostname() -> String {
    hostname::get()
        .ok()
        .and_then(|h| h.into_string().ok())
        .unwrap_or_else(|| "unknown-host".into())
}

pub fn primary_ipv4() -> Option<String> {
    let addrs = get_if_addrs().ok()?;
    // Prefer non-loopback IPv4
    for iface in addrs {
        if iface.is_loopback() { continue; }
        if let std::net::IpAddr::V4(v4) = iface.ip() { return Some(v4.to_string()); }
    }
    None
}
