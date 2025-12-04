#!/bin/bash
#
# Casper VPN Agent Uninstallation Script
#

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

INSTALL_DIR="/opt/casper-agent"
SERVICE_NAME="casper-agent"

print_info() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

if [ "$EUID" -ne 0 ]; then
    print_error "This script must be run as root (use sudo)"
    exit 1
fi

echo "=========================================="
echo "  Casper VPN Agent Uninstallation"
echo "=========================================="
echo ""

# Stop service
if systemctl is-active --quiet "$SERVICE_NAME"; then
    print_info "Stopping $SERVICE_NAME service..."
    systemctl stop "$SERVICE_NAME"
fi

# Disable service
if systemctl is-enabled --quiet "$SERVICE_NAME" 2>/dev/null; then
    print_info "Disabling $SERVICE_NAME service..."
    systemctl disable "$SERVICE_NAME"
fi

# Remove service file
if [ -f "/etc/systemd/system/${SERVICE_NAME}.service" ]; then
    print_info "Removing systemd service file..."
    rm -f "/etc/systemd/system/${SERVICE_NAME}.service"
    systemctl daemon-reload
fi

# Remove installation directory
if [ -d "$INSTALL_DIR" ]; then
    read -p "Remove all data from $INSTALL_DIR? [y/N]: " remove_data
    if [[ "$remove_data" =~ ^[Yy]$ ]]; then
        print_info "Removing $INSTALL_DIR..."
        rm -rf "$INSTALL_DIR"
        print_success "Removed $INSTALL_DIR"
    else
        print_info "Keeping $INSTALL_DIR (remove manually if needed)"
    fi
fi

echo ""
print_success "Casper Agent has been uninstalled"
