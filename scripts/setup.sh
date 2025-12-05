#!/bin/bash

# ============================================
# CasperVPN Setup Script
# ============================================
# This script sets up the CasperVPN environment
# for first-time deployment or fresh installations

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Setup Script${NC}"
echo -e "${GREEN}============================================${NC}"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}Please do not run this script as root${NC}"
    exit 1
fi

# Check for required commands
command -v docker >/dev/null 2>&1 || {
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
}

command -v docker-compose >/dev/null 2>&1 || {
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
}

echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Creating .env file from template...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ .env file created${NC}"
    echo -e "${YELLOW}⚠ IMPORTANT: Please edit .env and update the passwords and secrets!${NC}"
else
    echo -e "${GREEN}✓ .env file already exists${NC}"
fi

# Create necessary directories
echo -e "${YELLOW}Creating necessary directories...${NC}"
mkdir -p nginx/ssl
mkdir -p backups
mkdir -p logs
echo -e "${GREEN}✓ Directories created${NC}"

# Generate SSL certificates for local development
if [ ! -f "nginx/ssl/caspervpn.crt" ]; then
    echo -e "${YELLOW}Generating self-signed SSL certificates for local development...${NC}"
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout nginx/ssl/caspervpn.key \
        -out nginx/ssl/caspervpn.crt \
        -subj "/C=US/ST=State/L=City/O=CasperVPN/CN=caspervpn.local" \
        2>/dev/null
    echo -e "${GREEN}✓ SSL certificates generated${NC}"
else
    echo -e "${GREEN}✓ SSL certificates already exist${NC}"
fi

# Build Docker images
echo -e "${YELLOW}Building Docker images (this may take a while)...${NC}"
docker-compose build --parallel || {
    echo -e "${RED}Failed to build Docker images${NC}"
    exit 1
}
echo -e "${GREEN}✓ Docker images built successfully${NC}"

# Initialize database
echo -e "${YELLOW}Starting database...${NC}"
docker-compose up -d postgres redis
sleep 10
echo -e "${GREEN}✓ Database services started${NC}"

# Check database connection
echo -e "${YELLOW}Checking database connection...${NC}"
if docker-compose exec -T postgres pg_isready -U casperuser >/dev/null 2>&1; then
    echo -e "${GREEN}✓ Database is ready${NC}"
else
    echo -e "${RED}Failed to connect to database${NC}"
    exit 1
fi

# Add hosts entries
echo -e "${YELLOW}Adding hosts entries (requires sudo)...${NC}"
sudo bash -c 'cat >> /etc/hosts << EOF
# CasperVPN Local Development
127.0.0.1 caspervpn.local
127.0.0.1 api.caspervpn.local
127.0.0.1 admin.caspervpn.local
127.0.0.1 legacy.caspervpn.local
127.0.0.1 agent.caspervpn.local
127.0.0.1 grafana.caspervpn.local
EOF' 2>/dev/null || echo -e "${YELLOW}Could not add hosts entries. Please add them manually.${NC}"

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Setup Complete!${NC}"
echo -e "${GREEN}============================================${NC}"
echo -e ""
echo -e "Next steps:"
echo -e "1. Edit .env file and update passwords/secrets"
echo -e "2. Run: ${GREEN}./scripts/deploy.sh dev${NC} to start all services"
echo -e "3. Access services at:"
echo -e "   - Admin Panel: http://admin.caspervpn.local"
echo -e "   - API: http://api.caspervpn.local"
echo -e "   - Grafana: http://grafana.caspervpn.local:3001"
echo -e ""
