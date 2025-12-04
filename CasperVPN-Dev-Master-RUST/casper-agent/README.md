# Casper Agent (Rust)

A lightweight server-side agent that:
- Detects installed VPN protocols (WireGuard, OpenVPN, IKEv2/strongSwan, L2TP/xl2tpd)
- Collects system metrics (CPU, memory, disk, connected users, server load)
- Reports server info to Casper API and sends periodic status updates
- Fetches configuration settings from the API server
- Optionally provisions missing VPN components (stubs for now)

## Features

### Data Collection
The agent continuously monitors and reports:
- **CPU Usage**: Real-time processor utilization
- **Memory Usage**: RAM consumption percentage
- **Disk Usage**: Root partition disk space utilization
- **Connected Users**: Active VPN connections (WireGuard, OpenVPN, etc.)
- **Server Load**: Calculated from CPU (40%), Memory (40%), and User ratio (20%)

### Bidirectional Communication
- **Status Updates**: Sends metrics to `/api/Server/status` at regular intervals
- **Settings Sync**: Fetches configuration from `/api/Server/settings/{serverName}`
- **Initial Registration**: Registers server at startup via `/api/Server/create`

## Configuration

Create a `.env` file in the `casper-agent` directory (see `.env.example`):

```env
# Required
API_BASE_URL=http://localhost:5000

# Optional
API_TOKEN=your_secret_token_here
AUTO_PROVISION=true
SERVER_LOCATION=US-East
MAX_USERS=200
CONNECTION_TIMEOUT_SECS=30
HEALTH_CHECK_INTERVAL_SECS=60
```

### Environment Variables
- `API_BASE_URL` (required): Base URL of your Casper API (e.g., `http://localhost:5000` or `https://api.example.com`)
- `API_TOKEN` (optional): Bearer token for authenticated endpoints
- `AUTO_PROVISION` (optional, default `true`): Auto-install missing VPN protocols
- `SERVER_LOCATION` (optional): Geographic location (e.g., `US-East`, `EU-West`)
- `MAX_USERS` (optional, default `200`): Maximum concurrent users
- `CONNECTION_TIMEOUT_SECS` (optional, default `30`): Connection timeout in seconds
- `HEALTH_CHECK_INTERVAL_SECS` (optional, default `60`): How often to send status updates

## Installation

### Prerequisites
- Rust toolchain (1.70+): Install from [rustup.rs](https://rustup.rs/)
- Linux system (Ubuntu/Debian recommended for full functionality)

### Build
```bash
cd casper-agent
cargo build --release
```

The compiled binary will be at `target/release/casper-agent`

## Testing

### Local Testing (Without API Server)

1. **Mock API Server**: Use a simple HTTP server to test without the full ASP.NET backend

```bash
# Install a simple HTTP server
npm install -g json-server

# Create mock API data
echo '{"servers": []}' > db.json

# Start mock server on port 5000
json-server --watch db.json --port 5000
```

2. **Configure and Run**:
```bash
# Copy example config
cp .env.example .env

# Edit .env and set API_BASE_URL=http://localhost:5000
# Set HEALTH_CHECK_INTERVAL_SECS=10 for faster testing

# Run the agent
cargo run
```

### Testing with Full API Server

1. **Start the ASP.NET API**:
```bash
cd Casper.API
dotnet run
```

2. **Configure the Agent**:
```bash
cd casper-agent
cp .env.example .env

# Edit .env:
# API_BASE_URL=http://localhost:5166  # or your API port
# API_TOKEN=your_jwt_token  # if authentication is required
```

3. **Run the Agent**:
```bash
cargo run
```

### Testing on Linux

For full functionality (CPU/memory/disk metrics, VPN detection), test on a Linux system:

```bash
# On Ubuntu/Debian
sudo apt-get update

# Install VPN tools to test detection
sudo apt-get install wireguard wireguard-tools openvpn strongswan xl2tpd

# Run the agent
cargo run
```

### Testing Metrics Collection

The agent will output status updates every `HEALTH_CHECK_INTERVAL_SECS` seconds:

```
Server registered: my-server (192.168.1.100)
Starting periodic status updates every 60 seconds...
Status update sent - Load: 25%, CPU: 15.3%, Mem: 42.1%, Users: 5
Fetched settings - MaxUsers: 200, ConnTimeout: 30s, HealthCheck: 60s
```

### Verify API Communication

Check that the API receives data:

```bash
# Check server registration
curl http://localhost:5166/api/Server/all

# Manually send status update (replace with your server name)
curl -X PUT http://localhost:5166/api/Server/status \
  -H "Content-Type: application/json" \
  -d '{
    "serverName": "my-server",
    "serverStatus": 0,
    "connectedUsers": 5,
    "load": 25,
    "cpuUsage": 15.3,
    "memoryUsage": 42.1,
    "diskUsage": 58.5
  }'

# Fetch settings
curl http://localhost:5166/api/Server/settings/my-server
```

## Deployment

### Systemd Service (Linux)

Create `/etc/systemd/system/casper-agent.service`:

```ini
[Unit]
Description=Casper VPN Agent
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/opt/casper-agent
EnvironmentFile=/opt/casper-agent/.env
ExecStart=/opt/casper-agent/casper-agent
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable casper-agent
sudo systemctl start casper-agent
sudo systemctl status casper-agent
```

## Troubleshooting

### Agent won't start
- Check `API_BASE_URL` is set correctly in `.env`
- Verify API server is running and accessible
- Check logs: `journalctl -u casper-agent -f` (if using systemd)

### Metrics showing 0.0%
- Run on Linux for full metrics support
- Ensure `/proc/stat` and `/proc/meminfo` are readable
- Check `df` command works: `df -h /`

### VPN protocols not detected
- Install the VPN software packages
- Verify commands work: `wg`, `openvpn --version`, `strongswan version`

### API connection errors
- Verify API server is running: `curl http://localhost:5166/api/Server/all`
- Check firewall rules allow connections
- Ensure `API_TOKEN` is valid (if required)

## Notes
- Provisioning modules are stubs; implementation is distro-specific
- Enum values: `ConnectionProtocol` (OpenVPN=0, WireGuard=1, IKEv2=2), `ServerStatus` (Online=0, Maintenance=1, Offline=2)
- Windows support is limited; full functionality requires Linux
- Agent runs indefinitely, sending updates at configured intervals
