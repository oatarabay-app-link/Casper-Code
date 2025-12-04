#!/bin/bash
#
# Casper VPN Agent Installation Script
# Usage: curl -sSL https://your-domain.com/install.sh | sudo bash
# Or: wget -qO- https://your-domain.com/install.sh | sudo bash
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
INSTALL_DIR="/opt/casper-agent"
SERVICE_NAME="casper-agent"
BINARY_NAME="casper-agent"
GITHUB_REPO="${GITHUB_REPO:-}" # Set if downloading from GitHub releases
VERSION="${VERSION:-latest}"

# Functions
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_error "This script must be run as root (use sudo)"
        exit 1
    fi
}

detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS=$ID
        OS_VERSION=$VERSION_ID
    else
        print_error "Cannot detect OS. /etc/os-release not found."
        exit 1
    fi
    print_info "Detected OS: $OS $OS_VERSION"
}

check_dependencies() {
    print_info "Checking dependencies..."
    
    local missing_deps=()
    
    # Check for required tools
    for cmd in curl systemctl; do
        if ! command -v $cmd &> /dev/null; then
            missing_deps+=($cmd)
        fi
    done
    
    if [ ${#missing_deps[@]} -gt 0 ]; then
        print_error "Missing required dependencies: ${missing_deps[*]}"
        print_info "Installing dependencies..."
        
        case $OS in
            ubuntu|debian)
                apt-get update
                apt-get install -y curl systemd
                ;;
            centos|rhel|fedora)
                yum install -y curl systemd
                ;;
            *)
                print_error "Unsupported OS for automatic dependency installation"
                exit 1
                ;;
        esac
    fi
    
    print_success "Dependencies check passed"
}

install_rust() {
    if command -v rustc &> /dev/null; then
        print_info "Rust is already installed ($(rustc --version))"
        return 0
    fi
    
    print_info "Installing Rust..."
    curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y --default-toolchain stable
    source $HOME/.cargo/env
    print_success "Rust installed successfully"
}

download_binary() {
    print_info "Downloading Casper Agent binary..."
    
    if [ -n "$GITHUB_REPO" ]; then
        # Download from GitHub releases
        local download_url
        if [ "$VERSION" = "latest" ]; then
            download_url="https://github.com/${GITHUB_REPO}/releases/latest/download/casper-agent-linux-x86_64"
        else
            download_url="https://github.com/${GITHUB_REPO}/releases/download/${VERSION}/casper-agent-linux-x86_64"
        fi
        
        curl -L -o /tmp/casper-agent "$download_url"
        
        if [ $? -ne 0 ]; then
            print_error "Failed to download binary from GitHub"
            return 1
        fi
    else
        print_warning "GITHUB_REPO not set, skipping binary download"
        return 1
    fi
    
    print_success "Binary downloaded successfully"
    return 0
}

build_from_source() {
    print_info "Building Casper Agent from source..."
    
    # Check if source code is available
    if [ ! -f "Cargo.toml" ]; then
        print_error "Cargo.toml not found. Please run this script from the casper-agent directory or provide source code."
        exit 1
    fi
    
    # Install Rust if not present
    install_rust
    
    # Build release binary
    print_info "Compiling (this may take a few minutes)..."
    cargo build --release
    
    if [ $? -ne 0 ]; then
        print_error "Build failed"
        exit 1
    fi
    
    cp target/release/casper-agent /tmp/casper-agent
    print_success "Build completed successfully"
}

install_binary() {
    print_info "Installing Casper Agent to $INSTALL_DIR..."
    
    # Create installation directory
    mkdir -p "$INSTALL_DIR"
    
    # Copy binary
    if [ -f /tmp/casper-agent ]; then
        cp /tmp/casper-agent "$INSTALL_DIR/$BINARY_NAME"
        chmod +x "$INSTALL_DIR/$BINARY_NAME"
    else
        print_error "Binary not found at /tmp/casper-agent"
        exit 1
    fi
    
    print_success "Binary installed to $INSTALL_DIR/$BINARY_NAME"
}

configure_agent() {
    print_info "Configuring Casper Agent..."
    
    # Check if .env already exists
    if [ -f "$INSTALL_DIR/.env" ]; then
        print_warning ".env file already exists, skipping configuration"
        return 0
    fi
    
    # Interactive configuration
    echo ""
    echo "=== Casper Agent Configuration ==="
    echo ""
    
    read -p "API Base URL (e.g., https://api.example.com): " api_url
    while [ -z "$api_url" ]; do
        print_error "API Base URL is required"
        read -p "API Base URL: " api_url
    done
    
    read -p "API Token (optional, press Enter to skip): " api_token
    read -p "Server Location (e.g., US-East): " location
    location=${location:-"Unknown"}
    
    read -p "Max Users (default: 200): " max_users
    max_users=${max_users:-200}
    
    read -p "Health Check Interval in seconds (default: 60): " health_check
    health_check=${health_check:-60}
    
    # Create .env file
    cat > "$INSTALL_DIR/.env" <<EOF
# Casper VPN Agent Configuration
# Generated on $(date)

API_BASE_URL=$api_url
EOF
    
    if [ -n "$api_token" ]; then
        echo "API_TOKEN=$api_token" >> "$INSTALL_DIR/.env"
    fi
    
    cat >> "$INSTALL_DIR/.env" <<EOF
AUTO_PROVISION=true
SERVER_LOCATION=$location
MAX_USERS=$max_users
CONNECTION_TIMEOUT_SECS=30
HEALTH_CHECK_INTERVAL_SECS=$health_check
EOF
    
    chmod 600 "$INSTALL_DIR/.env"
    print_success "Configuration saved to $INSTALL_DIR/.env"
}

install_systemd_service() {
    print_info "Installing systemd service..."
    
    cat > "/etc/systemd/system/${SERVICE_NAME}.service" <<EOF
[Unit]
Description=Casper VPN Agent
Documentation=https://github.com/your-org/casper-agent
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
User=root
WorkingDirectory=$INSTALL_DIR
EnvironmentFile=$INSTALL_DIR/.env
ExecStart=$INSTALL_DIR/$BINARY_NAME
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal
SyslogIdentifier=casper-agent

# Security settings
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=$INSTALL_DIR

[Install]
WantedBy=multi-user.target
EOF
    
    # Reload systemd
    systemctl daemon-reload
    
    print_success "Systemd service installed"
}

install_vpn_protocols() {
    print_info "Would you like to install VPN protocols? (WireGuard, OpenVPN, etc.)"
    read -p "Install VPN protocols? [y/N]: " install_vpn
    
    if [[ ! "$install_vpn" =~ ^[Yy]$ ]]; then
        print_info "Skipping VPN protocol installation"
        return 0
    fi
    
    print_info "Installing VPN protocols..."
    
    case $OS in
        ubuntu|debian)
            apt-get update
            apt-get install -y wireguard wireguard-tools openvpn strongswan xl2tpd
            ;;
        centos|rhel)
            if [ "$OS_VERSION" -ge 8 ]; then
                yum install -y epel-release
                yum install -y wireguard-tools openvpn strongswan xl2tpd
            else
                print_warning "WireGuard not available in default repos for CentOS/RHEL < 8"
                yum install -y epel-release
                yum install -y openvpn strongswan xl2tpd
            fi
            ;;
        fedora)
            dnf install -y wireguard-tools openvpn strongswan xl2tpd
            ;;
        *)
            print_warning "Automatic VPN installation not supported for $OS"
            ;;
    esac
    
    print_success "VPN protocols installed"
}

start_service() {
    print_info "Starting Casper Agent service..."
    
    systemctl enable "$SERVICE_NAME"
    systemctl start "$SERVICE_NAME"
    
    sleep 2
    
    if systemctl is-active --quiet "$SERVICE_NAME"; then
        print_success "Casper Agent is running"
    else
        print_error "Failed to start Casper Agent"
        print_info "Check logs with: journalctl -u $SERVICE_NAME -f"
        exit 1
    fi
}

show_status() {
    echo ""
    echo "=========================================="
    echo "  Casper Agent Installation Complete!"
    echo "=========================================="
    echo ""
    print_info "Service Status:"
    systemctl status "$SERVICE_NAME" --no-pager -l
    echo ""
    print_info "Useful Commands:"
    echo "  View logs:      journalctl -u $SERVICE_NAME -f"
    echo "  Stop service:   systemctl stop $SERVICE_NAME"
    echo "  Start service:  systemctl start $SERVICE_NAME"
    echo "  Restart:        systemctl restart $SERVICE_NAME"
    echo "  Status:         systemctl status $SERVICE_NAME"
    echo "  Edit config:    nano $INSTALL_DIR/.env"
    echo ""
    print_success "Installation completed successfully!"
}

uninstall() {
    print_warning "Uninstalling Casper Agent..."
    
    # Stop and disable service
    systemctl stop "$SERVICE_NAME" 2>/dev/null || true
    systemctl disable "$SERVICE_NAME" 2>/dev/null || true
    
    # Remove service file
    rm -f "/etc/systemd/system/${SERVICE_NAME}.service"
    systemctl daemon-reload
    
    # Remove installation directory
    read -p "Remove configuration and data from $INSTALL_DIR? [y/N]: " remove_data
    if [[ "$remove_data" =~ ^[Yy]$ ]]; then
        rm -rf "$INSTALL_DIR"
        print_success "Removed $INSTALL_DIR"
    else
        print_info "Keeping $INSTALL_DIR"
    fi
    
    print_success "Casper Agent uninstalled"
    exit 0
}

# Main installation flow
main() {
    echo ""
    echo "=========================================="
    echo "  Casper VPN Agent Installation Script"
    echo "=========================================="
    echo ""
    
    # Check for uninstall flag
    if [ "$1" = "--uninstall" ] || [ "$1" = "-u" ]; then
        uninstall
    fi
    
    check_root
    detect_os
    check_dependencies
    
    # Try to download binary, if fails build from source
    if ! download_binary; then
        build_from_source
    fi
    
    install_binary
    configure_agent
    install_systemd_service
    install_vpn_protocols
    start_service
    show_status
}

# Run main function
main "$@"
