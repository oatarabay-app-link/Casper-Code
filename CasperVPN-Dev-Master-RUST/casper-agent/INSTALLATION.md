# Casper Agent Installation Guide

## Installation Methods

### Method 1: One-Line Install (Recommended)

If you host the installation script on your server:

```bash
curl -sSL https://your-domain.com/install.sh | sudo bash
```

Or using wget:
```bash
wget -qO- https://your-domain.com/install.sh | sudo bash
```

### Method 2: Manual Installation from Source

#### Prerequisites
- Linux server (Ubuntu 20.04+, Debian 10+, CentOS 8+, or Fedora)
- Root or sudo access
- Internet connection

#### Steps

1. **Clone or download the repository**
```bash
cd /tmp
# If using git
git clone https://github.com/your-org/CasperVPN.git
cd CasperVPN/casper-agent

# Or download and extract a release tarball
```

2. **Run the installation script**
```bash
sudo chmod +x install.sh
sudo ./install.sh
```

3. **Follow the interactive prompts**
```
API Base URL: https://api.casper-vpn.com
API Token: your_token_here
Server Location: US-East
Max Users: 200
Health Check Interval: 60
```

4. **Verify installation**
```bash
sudo systemctl status casper-agent
```

### Method 3: From GitHub Release Binary

```bash
# Download latest release
curl -L -o casper-agent https://github.com/your-org/CasperVPN/releases/latest/download/casper-agent-linux-x86_64

# Make executable
chmod +x casper-agent

# Move to system location
sudo mv casper-agent /usr/local/bin/

# Run manually or set up as service
casper-agent
```

### Method 4: Docker Installation

```bash
docker run -d \
  --name casper-agent \
  --restart unless-stopped \
  -e API_BASE_URL=https://api.casper-vpn.com \
  -e API_TOKEN=your_token_here \
  -e SERVER_LOCATION=US-East \
  your-org/casper-agent:latest
```

## Installation Script Features

The `install.sh` script automatically:
- ✅ Detects your operating system
- ✅ Installs required dependencies
- ✅ Downloads pre-built binary or builds from source
- ✅ Sets up systemd service for auto-start
- ✅ Creates configuration file interactively
- ✅ Optionally installs VPN protocols (WireGuard, OpenVPN, etc.)
- ✅ Starts and enables the service

## Configuration

### Interactive Configuration
The install script will prompt you for:
- **API Base URL** (required): Your Casper API server
- **API Token** (optional): Authentication token
- **Server Location**: Geographic location identifier
- **Max Users**: Maximum concurrent connections
- **Health Check Interval**: Status update frequency (seconds)

### Manual Configuration
Edit `/opt/casper-agent/.env`:

```bash
sudo nano /opt/casper-agent/.env
```

Example configuration:
```env
API_BASE_URL=https://api.casper-vpn.com
API_TOKEN=your_secret_token
AUTO_PROVISION=true
SERVER_LOCATION=US-East
MAX_USERS=200
CONNECTION_TIMEOUT_SECS=30
HEALTH_CHECK_INTERVAL_SECS=60
```

After editing, restart the service:
```bash
sudo systemctl restart casper-agent
```

## Post-Installation

### Check Service Status
```bash
sudo systemctl status casper-agent
```

### View Logs
```bash
# Real-time logs
sudo journalctl -u casper-agent -f

# Last 100 lines
sudo journalctl -u casper-agent -n 100

# Logs since yesterday
sudo journalctl -u casper-agent --since yesterday
```

### Service Management
```bash
# Start service
sudo systemctl start casper-agent

# Stop service
sudo systemctl stop casper-agent

# Restart service
sudo systemctl restart casper-agent

# Enable auto-start on boot
sudo systemctl enable casper-agent

# Disable auto-start
sudo systemctl disable casper-agent
```

### Test Connection
```bash
# Test if agent can reach API
curl -I https://your-api-url.com/api/Server/all

# Check server registration
curl https://your-api-url.com/api/Server/all | jq
```

## Installing VPN Protocols

### Manual Installation

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install -y wireguard wireguard-tools openvpn strongswan xl2tpd
```

**CentOS/RHEL 8+:**
```bash
sudo yum install -y epel-release
sudo yum install -y wireguard-tools openvpn strongswan xl2tpd
```

**Fedora:**
```bash
sudo dnf install -y wireguard-tools openvpn strongswan xl2tpd
```

### Verify Installation
```bash
# Check WireGuard
wg --version

# Check OpenVPN
openvpn --version

# Check strongSwan
strongswan version

# Check xl2tpd
xl2tpd --version
```

## Uninstallation

### Using the uninstall script
```bash
cd /tmp/CasperVPN/casper-agent
sudo ./uninstall.sh
```

### Using the install script
```bash
sudo ./install.sh --uninstall
```

### Manual Uninstallation
```bash
# Stop and disable service
sudo systemctl stop casper-agent
sudo systemctl disable casper-agent

# Remove service file
sudo rm /etc/systemd/system/casper-agent.service
sudo systemctl daemon-reload

# Remove installation directory
sudo rm -rf /opt/casper-agent

# Remove binary (if installed manually)
sudo rm /usr/local/bin/casper-agent
```

## Troubleshooting

### Service won't start
```bash
# Check detailed error logs
sudo journalctl -u casper-agent -n 50 --no-pager

# Verify configuration
sudo cat /opt/casper-agent/.env

# Test binary manually
sudo /opt/casper-agent/casper-agent
```

### API connection issues
```bash
# Test network connectivity
ping api.your-domain.com

# Test API endpoint
curl -v https://api.your-domain.com/api/Server/all

# Check firewall
sudo iptables -L
sudo firewall-cmd --list-all  # CentOS/RHEL
```

### Permission errors
```bash
# Fix ownership
sudo chown -R root:root /opt/casper-agent

# Fix permissions
sudo chmod 755 /opt/casper-agent
sudo chmod 755 /opt/casper-agent/casper-agent
sudo chmod 600 /opt/casper-agent/.env
```

### Reinstallation
```bash
# Uninstall first
sudo ./uninstall.sh

# Then install again
sudo ./install.sh
```

## Upgrading

### Upgrade to latest version
```bash
# Stop service
sudo systemctl stop casper-agent

# Download new binary
cd /tmp
curl -L -o casper-agent https://github.com/your-org/CasperVPN/releases/latest/download/casper-agent-linux-x86_64

# Replace old binary
sudo mv casper-agent /opt/casper-agent/casper-agent
sudo chmod +x /opt/casper-agent/casper-agent

# Start service
sudo systemctl start casper-agent

# Verify
sudo systemctl status casper-agent
```

## Security Considerations

- The agent runs as root (required for VPN management)
- Store API tokens securely in `.env` file (mode 600)
- Use HTTPS for API communication
- Consider firewall rules to restrict API access
- Regularly update the agent to latest version
- Monitor logs for suspicious activity

## Support

For issues, questions, or contributions:
- GitHub Issues: https://github.com/your-org/CasperVPN/issues
- Documentation: https://docs.casper-vpn.com
- Email: support@casper-vpn.com
