# CasperVPN Deployment Guide

**Version:** 1.0  
**Last Updated:** December 9, 2025  
**Target Environment:** Production  
**Estimated Deployment Time:** 8-10 days

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Environment Setup](#environment-setup)
4. [Backend API Deployment](#backend-api-deployment)
5. [Admin Panel Deployment](#admin-panel-deployment)
6. [iOS App Deployment](#ios-app-deployment)
7. [DevOps & Infrastructure](#devops--infrastructure)
8. [Post-Deployment Verification](#post-deployment-verification)
9. [Rollback Procedures](#rollback-procedures)
10. [Troubleshooting](#troubleshooting)
11. [Production Checklist](#production-checklist)
12. [Monitoring & Maintenance](#monitoring--maintenance)

---

## Overview

This guide covers the complete deployment process for all CasperVPN components:

- **Backend API** (.NET Core 8.0) - REST API with 46 endpoints
- **Admin Panel** (React + TypeScript) - Management dashboard
- **iOS App** (Swift + SwiftUI) - Native VPN client
- **Infrastructure** (Docker + Nginx + Monitoring) - Production environment

### Deployment Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Production Environment                   │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Load Balancer / CDN (Cloudflare/AWS CloudFront)    │  │
│  └────────────────────┬─────────────────────────────────┘  │
│                       │                                      │
│  ┌────────────────────▼─────────────────────────────────┐  │
│  │               Nginx Reverse Proxy                     │  │
│  │  - SSL/TLS Termination                               │  │
│  │  - Rate Limiting                                      │  │
│  │  - Load Balancing                                     │  │
│  └──────┬────────────────────┬─────────────────────────┘  │
│         │                    │                              │
│  ┌──────▼───────┐    ┌──────▼──────┐                      │
│  │  Backend API │    │ Admin Panel │                      │
│  │  (Docker)    │    │  (Docker)   │                      │
│  │  Port: 5000  │    │  Port: 3000 │                      │
│  └──────┬───────┘    └─────────────┘                      │
│         │                                                   │
│  ┌──────▼────────────────────────┐                        │
│  │      PostgreSQL Database       │                        │
│  │      Port: 5432                │                        │
│  │      Persistent Volume         │                        │
│  └───────────────────────────────┘                        │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              Monitoring Stack                         │  │
│  │  Prometheus (9090) + Grafana (3001) + Alertmanager  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## Prerequisites

### System Requirements

#### Production Server
- **OS:** Ubuntu 22.04 LTS (recommended) or Ubuntu 20.04 LTS
- **CPU:** 4 cores minimum (8 cores recommended)
- **RAM:** 8 GB minimum (16 GB recommended)
- **Storage:** 100 GB SSD minimum (500 GB recommended)
- **Network:** 1 Gbps network connection
- **IPv4:** Static public IP address

#### Development Machine
- **OS:** macOS 13+ (for iOS development) or Linux/Windows (for backend/admin)
- **CPU:** 4 cores minimum
- **RAM:** 8 GB minimum (16 GB recommended for iOS development)

### Required Software

#### Server Software
```bash
# Core
- Docker 24.0+
- Docker Compose 2.20+
- Nginx 1.24+
- Git 2.30+

# Database
- PostgreSQL 15+

# SSL
- Certbot (Let's Encrypt)

# Monitoring
- Prometheus
- Grafana
- Alertmanager
```

#### Development Tools
```bash
# Backend Development
- .NET Core SDK 8.0
- Visual Studio Code or Rider

# Admin Panel Development
- Node.js 18+ (LTS)
- npm 9+ or yarn 1.22+

# iOS Development
- Xcode 15+
- Swift 5.9+
- CocoaPods (if using pods)
```

### Required Accounts & Services

- [x] **GitHub Account** - Repository access
- [x] **Domain Name** - Registered domain (e.g., caspervpn.com)
- [x] **SSL Certificate Provider** - Let's Encrypt (free) or commercial
- [x] **Apple Developer Account** - $99/year for iOS app distribution
- [x] **Stripe Account** - Payment processing
- [x] **SMTP Service** - Email delivery (SendGrid, Mailgun, or Gmail)
- [x] **Cloud Storage** (Optional) - Backups (AWS S3, DigitalOcean Spaces)

### Required Credentials

Document these securely before deployment:

```bash
# Database
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_NAME=caspervpn
DATABASE_USER=caspervpn_user
DATABASE_PASSWORD=<secure-password>

# JWT
JWT_SECRET=<generate-256-bit-secret>
JWT_ISSUER=https://api.caspervpn.com
JWT_AUDIENCE=https://api.caspervpn.com
JWT_EXPIRY_HOURS=1

# Stripe
STRIPE_SECRET_KEY=sk_live_...
STRIPE_PUBLISHABLE_KEY=pk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Email (SMTP)
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASSWORD=<sendgrid-api-key>
SMTP_FROM_EMAIL=noreply@caspervpn.com
SMTP_FROM_NAME=CasperVPN

# Admin Credentials
ADMIN_EMAIL=admin@caspervpn.com
ADMIN_PASSWORD=<secure-password>
ADMIN_FIRST_NAME=System
ADMIN_LAST_NAME=Administrator

# API Keys
FREERADIUS_HOST=radius.caspervpn.com
FREERADIUS_SECRET=<radius-secret>
FREERADIUS_PORT=1812

# App Configuration
API_BASE_URL=https://api.caspervpn.com
ADMIN_PANEL_URL=https://admin.caspervpn.com
CORS_ORIGINS=https://admin.caspervpn.com,https://www.caspervpn.com
```

---

## Environment Setup

### Step 1: Server Provisioning

#### Option A: DigitalOcean Droplet
```bash
# Create droplet
# - Choose: Ubuntu 22.04 LTS
# - Plan: Premium Intel 8 GB / 4 CPUs
# - Region: Choose closest to target users
# - Additional: Enable monitoring, IPv6, backups

# SSH into server
ssh root@your-server-ip
```

#### Option B: AWS EC2
```bash
# Launch EC2 instance
# - AMI: Ubuntu Server 22.04 LTS
# - Instance Type: t3.xlarge (4 vCPUs, 16 GB RAM)
# - Storage: 100 GB GP3 SSD
# - Security Group: Open ports 80, 443, 22

# SSH into server
ssh -i your-key.pem ubuntu@your-server-ip
```

#### Option C: Bare Metal / VPS
```bash
# Ensure Ubuntu 22.04 LTS is installed
# SSH access configured
# Root or sudo access available
```

### Step 2: Initial Server Configuration

#### Update System
```bash
# Update package list
sudo apt update

# Upgrade packages
sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git vim htop net-tools ufw
```

#### Configure Firewall
```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow PostgreSQL (only from localhost)
# sudo ufw allow from 127.0.0.1 to any port 5432

# Check status
sudo ufw status
```

#### Install Docker
```bash
# Remove old versions
sudo apt remove docker docker-engine docker.io containerd runc

# Install dependencies
sudo apt install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

# Add Docker's official GPG key
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
    sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

# Set up repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Verify installation
docker --version
docker compose version

# Add user to docker group (optional, for non-root usage)
sudo usermod -aG docker $USER
newgrp docker
```

#### Install Nginx
```bash
# Install Nginx
sudo apt install -y nginx

# Verify installation
nginx -v

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
```

### Step 3: Clone Repository
```bash
# Navigate to home directory
cd /home/ubuntu

# Clone repository
git clone https://github.com/oatarabay-app-link/Casper-Code.git

# Navigate to project
cd Casper-Code

# Checkout main branch
git checkout main
```

### Step 4: Environment Configuration

#### Create Environment Files
```bash
# Backend API environment
cat > backend-dotnet-core/.env << 'EOF'
# Database
ConnectionStrings__DefaultConnection=Host=postgres;Port=5432;Database=caspervpn;Username=caspervpn_user;Password=${DB_PASSWORD}

# JWT
Jwt__Secret=${JWT_SECRET}
Jwt__Issuer=https://api.caspervpn.com
Jwt__Audience=https://api.caspervpn.com
Jwt__ExpiryHours=1

# Stripe
Stripe__SecretKey=${STRIPE_SECRET_KEY}
Stripe__PublishableKey=${STRIPE_PUBLISHABLE_KEY}
Stripe__WebhookSecret=${STRIPE_WEBHOOK_SECRET}

# Email
Email__SmtpHost=${SMTP_HOST}
Email__SmtpPort=${SMTP_PORT}
Email__SmtpUser=${SMTP_USER}
Email__SmtpPassword=${SMTP_PASSWORD}
Email__FromEmail=${SMTP_FROM_EMAIL}
Email__FromName=${SMTP_FROM_NAME}

# FreeRADIUS
FreeRadius__Host=${FREERADIUS_HOST}
FreeRadius__Secret=${FREERADIUS_SECRET}
FreeRadius__Port=${FREERADIUS_PORT}

# App Settings
ASPNETCORE_ENVIRONMENT=Production
ASPNETCORE_URLS=http://+:5000
EOF

# Admin Panel environment
cat > admin-panel-react/.env << 'EOF'
REACT_APP_API_URL=https://api.caspervpn.com
REACT_APP_STRIPE_PUBLISHABLE_KEY=${STRIPE_PUBLISHABLE_KEY}
NODE_ENV=production
EOF

# Docker Compose environment
cat > .env << 'EOF'
# PostgreSQL
POSTGRES_USER=caspervpn_user
POSTGRES_PASSWORD=${DB_PASSWORD}
POSTGRES_DB=caspervpn

# API
API_PORT=5000

# Admin Panel
ADMIN_PORT=3000

# Monitoring
PROMETHEUS_PORT=9090
GRAFANA_PORT=3001
ALERTMANAGER_PORT=9093
EOF
```

**Important:** Replace `${VARIABLE}` placeholders with actual values.

---

## Backend API Deployment

### Step 1: Database Setup

#### Install PostgreSQL (if not using Docker)
```bash
# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Start and enable PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Check status
sudo systemctl status postgresql
```

#### Create Database and User
```bash
# Switch to postgres user
sudo -u postgres psql

# Create database
CREATE DATABASE caspervpn;

# Create user
CREATE USER caspervpn_user WITH PASSWORD 'your-secure-password';

# Grant privileges
GRANT ALL PRIVILEGES ON DATABASE caspervpn TO caspervpn_user;

# Exit
\q
```

#### Configure PostgreSQL (Production Settings)
```bash
# Edit PostgreSQL configuration
sudo vim /etc/postgresql/15/main/postgresql.conf

# Update these settings:
max_connections = 200
shared_buffers = 2GB
effective_cache_size = 6GB
maintenance_work_mem = 512MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200
work_mem = 5242kB
min_wal_size = 1GB
max_wal_size = 4GB

# Restart PostgreSQL
sudo systemctl restart postgresql
```

### Step 2: Build Backend API

#### Using Docker (Recommended)
```bash
# Navigate to backend directory
cd backend-dotnet-core

# Build Docker image
docker build -t caspervpn/backend-api:latest .

# Verify image
docker images | grep caspervpn/backend-api
```

#### Using .NET CLI (Alternative)
```bash
# Navigate to backend directory
cd backend-dotnet-core

# Restore dependencies
dotnet restore

# Build for production
dotnet publish -c Release -o ./publish

# Verify build
ls -la ./publish
```

### Step 3: Run Database Migrations

#### Using Docker
```bash
# Run migrations in temporary container
docker run --rm \
  --network host \
  -e ConnectionStrings__DefaultConnection="Host=localhost;Port=5432;Database=caspervpn;Username=caspervpn_user;Password=your-password" \
  caspervpn/backend-api:latest \
  dotnet ef database update
```

#### Using .NET CLI
```bash
# Navigate to backend directory
cd backend-dotnet-core

# Update database
dotnet ef database update

# Verify migrations
dotnet ef migrations list
```

### Step 4: Seed Initial Data

#### Create Admin User and Plans
```bash
# Create seed script
cat > backend-dotnet-core/seed.sql << 'EOF'
-- Create admin user (password: Admin@123 - CHANGE THIS!)
INSERT INTO users (id, email, password_hash, first_name, last_name, role, email_verified, is_active, created_at, updated_at)
VALUES (
  gen_random_uuid(),
  'admin@caspervpn.com',
  '$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5QBVbz/2Vm1FG', -- BCrypt hash of "Admin@123"
  'System',
  'Administrator',
  'SuperAdmin',
  true,
  true,
  NOW(),
  NOW()
);

-- Create Free Plan
INSERT INTO plans (id, name, description, price, billing_interval, data_limit, device_limit, features, is_active, created_at, updated_at)
VALUES (
  gen_random_uuid(),
  'Free',
  'Basic VPN access with limited features',
  0.00,
  'Monthly',
  524288000, -- 500 MB in bytes
  1,
  '{"basic_servers": true, "customer_support": false, "ad_blocking": false}',
  true,
  NOW(),
  NOW()
);

-- Create Premium Monthly Plan
INSERT INTO plans (id, name, description, price, billing_interval, data_limit, device_limit, features, is_active, created_at, updated_at)
VALUES (
  gen_random_uuid(),
  'Premium Monthly',
  'Unlimited data with all features',
  9.99,
  'Monthly',
  NULL, -- Unlimited
  5,
  '{"all_servers": true, "customer_support": true, "ad_blocking": true, "streaming": true, "p2p": true}',
  true,
  NOW(),
  NOW()
);

-- Create Premium Yearly Plan
INSERT INTO plans (id, name, description, price, billing_interval, data_limit, device_limit, features, is_active, created_at, updated_at)
VALUES (
  gen_random_uuid(),
  'Premium Yearly',
  'Unlimited data with all features - Save 33%!',
  79.99,
  'Yearly',
  NULL, -- Unlimited
  5,
  '{"all_servers": true, "customer_support": true, "ad_blocking": true, "streaming": true, "p2p": true, "priority_support": true}',
  true,
  NOW(),
  NOW()
);

-- Create Sample VPN Servers
INSERT INTO vpn_servers (id, name, country, country_code, city, hostname, ip_address, port, public_key, protocol, capacity, current_load, is_active, is_premium, created_at, updated_at)
VALUES
  (gen_random_uuid(), 'US-NY-01', 'United States', 'US', 'New York', 'us-ny-01.caspervpn.com', '192.0.2.1', 51820, 'sample-public-key-1', 'WireGuard', 1000, 45, true, false, NOW(), NOW()),
  (gen_random_uuid(), 'UK-LON-01', 'United Kingdom', 'GB', 'London', 'uk-lon-01.caspervpn.com', '192.0.2.2', 51820, 'sample-public-key-2', 'WireGuard', 1000, 32, true, false, NOW(), NOW()),
  (gen_random_uuid(), 'DE-BER-01', 'Germany', 'DE', 'Berlin', 'de-ber-01.caspervpn.com', '192.0.2.3', 51820, 'sample-public-key-3', 'WireGuard', 1000, 28, true, true, NOW(), NOW()),
  (gen_random_uuid(), 'JP-TOK-01', 'Japan', 'JP', 'Tokyo', 'jp-tok-01.caspervpn.com', '192.0.2.4', 51820, 'sample-public-key-4', 'WireGuard', 1000, 67, true, true, NOW(), NOW()),
  (gen_random_uuid(), 'SG-SIN-01', 'Singapore', 'SG', 'Singapore', 'sg-sin-01.caspervpn.com', '192.0.2.5', 51820, 'sample-public-key-5', 'WireGuard', 1000, 52, true, true, NOW(), NOW());
EOF

# Execute seed script
psql -h localhost -U caspervpn_user -d caspervpn -f backend-dotnet-core/seed.sql
```

**Important:** Change the default admin password immediately after first login!

### Step 5: Configure Nginx for Backend API

```bash
# Create Nginx configuration
sudo cat > /etc/nginx/sites-available/caspervpn-api << 'EOF'
upstream backend_api {
    server 127.0.0.1:5000;
    keepalive 32;
}

server {
    listen 80;
    server_name api.caspervpn.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.caspervpn.com;

    # SSL Configuration (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/api.caspervpn.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.caspervpn.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Logging
    access_log /var/log/nginx/api.caspervpn.com.access.log;
    error_log /var/log/nginx/api.caspervpn.com.error.log;

    # Max body size (for file uploads)
    client_max_body_size 10M;

    # Proxy to backend API
    location / {
        proxy_pass http://backend_api;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
    }

    # Health check endpoint
    location /health {
        proxy_pass http://backend_api/health;
        access_log off;
    }

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=api_limit:10m rate=100r/m;
    limit_req zone=api_limit burst=20 nodelay;
}
EOF

# Enable site
sudo ln -s /etc/nginx/sites-available/caspervpn-api /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### Step 6: SSL Certificate Setup (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate for API domain
sudo certbot --nginx -d api.caspervpn.com

# Follow prompts:
# - Enter email address
# - Agree to terms
# - Choose to redirect HTTP to HTTPS (option 2)

# Verify auto-renewal
sudo certbot renew --dry-run

# Check renewal timer
sudo systemctl status certbot.timer
```

### Step 7: Start Backend API

#### Using Docker Compose (Recommended)
```bash
# Navigate to project root
cd /home/ubuntu/Casper-Code

# Start services
docker compose up -d backend-api postgres

# Check logs
docker compose logs -f backend-api

# Verify containers are running
docker compose ps
```

#### Using Systemd Service (Alternative)
```bash
# Create systemd service
sudo cat > /etc/systemd/system/caspervpn-api.service << 'EOF'
[Unit]
Description=CasperVPN Backend API
After=network.target postgresql.service

[Service]
Type=notify
WorkingDirectory=/home/ubuntu/Casper-Code/backend-dotnet-core/publish
ExecStart=/usr/bin/dotnet /home/ubuntu/Casper-Code/backend-dotnet-core/publish/CasperVPN.API.dll
Restart=always
RestartSec=10
User=www-data
Environment=ASPNETCORE_ENVIRONMENT=Production
Environment=ASPNETCORE_URLS=http://localhost:5000

[Install]
WantedBy=multi-user.target
EOF

# Reload systemd
sudo systemctl daemon-reload

# Start service
sudo systemctl start caspervpn-api

# Enable on boot
sudo systemctl enable caspervpn-api

# Check status
sudo systemctl status caspervpn-api
```

### Step 8: Verify Backend API Deployment

```bash
# Test health endpoint
curl https://api.caspervpn.com/health

# Expected output:
# {"status":"Healthy","timestamp":"2025-12-09T..."}

# Test API status endpoint
curl https://api.caspervpn.com/api/api/status

# Test authentication endpoint (should return validation error)
curl -X POST https://api.caspervpn.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test"}'

# Check Swagger documentation
# Open in browser: https://api.caspervpn.com/swagger
```

---

## Admin Panel Deployment

### Step 1: Build Admin Panel

#### Using Docker (Recommended)
```bash
# Navigate to admin panel directory
cd admin-panel-react

# Build Docker image
docker build -t caspervpn/admin-panel:latest .

# Verify image
docker images | grep caspervpn/admin-panel
```

#### Using Node.js (Alternative)
```bash
# Navigate to admin panel directory
cd admin-panel-react

# Install dependencies
npm install

# Build for production
npm run build

# Verify build
ls -la build/
```

### Step 2: Configure Nginx for Admin Panel

```bash
# Create Nginx configuration
sudo cat > /etc/nginx/sites-available/caspervpn-admin << 'EOF'
server {
    listen 80;
    server_name admin.caspervpn.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name admin.caspervpn.com;

    # SSL Configuration (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/admin.caspervpn.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/admin.caspervpn.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Logging
    access_log /var/log/nginx/admin.caspervpn.com.access.log;
    error_log /var/log/nginx/admin.caspervpn.com.error.log;

    # Root directory
    root /var/www/admin.caspervpn.com;
    index index.html;

    # Serve static files
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;
}
EOF

# Enable site
sudo ln -s /etc/nginx/sites-available/caspervpn-admin /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### Step 3: SSL Certificate for Admin Panel

```bash
# Obtain certificate for admin domain
sudo certbot --nginx -d admin.caspervpn.com

# Follow prompts
```

### Step 4: Deploy Admin Panel Files

#### Using Docker (Recommended)
```bash
# Start admin panel container
docker compose up -d admin-panel

# Check logs
docker compose logs -f admin-panel
```

#### Using Static Files (Alternative)
```bash
# Create web directory
sudo mkdir -p /var/www/admin.caspervpn.com

# Copy build files
sudo cp -r admin-panel-react/build/* /var/www/admin.caspervpn.com/

# Set permissions
sudo chown -R www-data:www-data /var/www/admin.caspervpn.com
sudo chmod -R 755 /var/www/admin.caspervpn.com

# Verify files
ls -la /var/www/admin.caspervpn.com/
```

### Step 5: Verify Admin Panel Deployment

```bash
# Open in browser
# https://admin.caspervpn.com

# Test login
# Email: admin@caspervpn.com
# Password: Admin@123 (change this!)

# Check console for errors
# Verify API connection
# Test all 8 pages:
# - Dashboard
# - Users
# - Servers
# - Subscriptions
# - Payments
# - Analytics
# - Settings
# - Login
```

---

## iOS App Deployment

### Step 1: Xcode Project Setup

```bash
# On macOS development machine

# Clone repository
git clone https://github.com/oatarabay-app-link/Casper-Code.git
cd Casper-Code/ios-app-v2/CasperVPN

# Open in Xcode
open CasperVPN.xcodeproj
```

### Step 2: Configure Project Settings

#### Signing & Capabilities

1. **Select Project** → CasperVPN target
2. **Signing & Capabilities tab**
3. **Enable Automatic Signing**
   - Team: Select your Apple Developer team
   - Bundle Identifier: `com.caspervpn.ios` (or your custom identifier)

4. **Add Capabilities:**
   - Click `+ Capability`
   - Add **Network Extensions**
   - Add **Personal VPN**
   - Add **Keychain Sharing**
   - Add **App Groups** (create group: `group.com.caspervpn.shared`)

#### Packet Tunnel Extension Target

1. **Select PacketTunnel target**
2. **Signing & Capabilities tab**
3. **Enable Automatic Signing**
   - Same team as main app
   - Bundle Identifier: `com.caspervpn.ios.PacketTunnel`

4. **Add Capabilities:**
   - **Network Extensions**
   - **Personal VPN**
   - **Keychain Sharing** (same group)
   - **App Groups** (same group)

### Step 3: Configure API Endpoint

```swift
// Edit: CasperVPN/App/Config.swift

enum Config {
    // Production API
    static let apiBaseURL = "https://api.caspervpn.com"
    static let apiVersion = "v1"
    
    // App Configuration
    static let appName = "CasperVPN"
    static let appVersion = "1.0.0"
    
    // Stripe (for in-app purchases, if implemented)
    static let stripePublishableKey = "pk_live_..."
}
```

### Step 4: Build for Testing

```bash
# In Xcode:

# 1. Select target device or simulator
# Product > Destination > Select iPhone/iPad

# 2. Build project
# Product > Build (⌘B)

# 3. Run on device
# Product > Run (⌘R)

# 4. Test VPN connection
# - Login with test credentials
# - Select a server
# - Connect to VPN
# - Verify connection
# - Disconnect
```

### Step 5: TestFlight Distribution

#### Prepare for Archive

```bash
# 1. Clean build folder
# Product > Clean Build Folder (⇧⌘K)

# 2. Select "Any iOS Device (arm64)"
# Product > Destination > Any iOS Device

# 3. Archive
# Product > Archive

# Wait for archive to complete (~5-10 minutes)
```

#### Upload to App Store Connect

```bash
# 1. In Organizer (Window > Organizer)
# 2. Select the archive
# 3. Click "Distribute App"
# 4. Select "App Store Connect"
# 5. Click "Upload"
# 6. Select signing options:
#    - Automatically manage signing (recommended)
# 7. Review and upload

# Upload time: ~10-30 minutes depending on connection
```

#### Configure TestFlight

```bash
# 1. Go to App Store Connect
# https://appstoreconnect.apple.com

# 2. Select CasperVPN app

# 3. TestFlight tab

# 4. Internal Testing:
#    - Add internal testers (up to 100)
#    - Internal testers get immediate access

# 5. External Testing:
#    - Add external testers (up to 10,000)
#    - Requires Beta App Review (~24-48 hours)

# 6. Fill Test Information:
#    - Test Description
#    - Feedback Email
#    - What to Test
```

#### Beta Testing Checklist

- [ ] **Functionality Testing**
  - [ ] User registration and login
  - [ ] Server list loading
  - [ ] Server selection
  - [ ] VPN connection
  - [ ] VPN disconnection
  - [ ] Kill switch
  - [ ] Auto-reconnect
  - [ ] Favorites
  - [ ] Recent servers
  - [ ] Settings

- [ ] **Performance Testing**
  - [ ] App launch time
  - [ ] Server list load time
  - [ ] Connection speed
  - [ ] Data usage accuracy
  - [ ] Battery drain
  - [ ] Memory usage

- [ ] **Compatibility Testing**
  - [ ] iOS 15.0
  - [ ] iOS 16.0
  - [ ] iOS 17.0
  - [ ] iOS 18.0 (latest)
  - [ ] iPhone SE (small screen)
  - [ ] iPhone 15 Pro Max (large screen)
  - [ ] iPad (tablet layout)

### Step 6: App Store Submission

#### Prepare App Metadata

```markdown
# App Name
CasperVPN - Secure VPN Proxy

# Subtitle
Fast, Secure, and Private VPN

# Description
CasperVPN provides fast, secure, and private VPN access with military-grade encryption. Protect your privacy and browse the internet freely with our global network of servers.

**Features:**
• 🔒 Military-grade encryption (WireGuard)
• 🌍 Global server network (50+ locations)
• ⚡ Lightning-fast speeds
• 📵 Kill switch protection
• 🔄 Auto-reconnect
• ⭐ Favorite servers
• 📊 Real-time statistics
• 🎯 Smart server selection
• 📱 Clean, intuitive interface
• 🔐 No-logs policy

**Plans:**
• Free: 500 MB data, basic servers
• Premium: Unlimited data, all servers, priority support

**Why CasperVPN?**
• Trusted by thousands worldwide
• 24/7 customer support
• 30-day money-back guarantee
• Easy to use, no technical knowledge required

Download CasperVPN today and experience internet freedom!

# Keywords
vpn, proxy, security, privacy, encryption, wireguard, secure, anonymous, protection

# Support URL
https://www.caspervpn.com/support

# Privacy Policy URL
https://www.caspervpn.com/privacy

# Terms of Use
https://www.caspervpn.com/terms
```

#### Prepare Screenshots

**Required Screenshots:**
- **6.7" Display** (iPhone 15 Pro Max): 3-10 screenshots
- **6.5" Display** (iPhone 14 Plus): 3-10 screenshots
- **5.5" Display** (iPhone 8 Plus): 3-10 screenshots
- **12.9" Display** (iPad Pro): 3-10 screenshots (if iPad supported)

**Screenshot Suggestions:**
1. Login screen
2. Server list
3. Connection screen (connected state)
4. Server details
5. Statistics view
6. Settings page

#### Submit for Review

```bash
# 1. Go to App Store Connect

# 2. Select CasperVPN app

# 3. App Store tab

# 4. + Version or Platform > iOS

# 5. Fill out all required fields:
#    - Version number (1.0.0)
#    - Copyright
#    - Category (Utilities)
#    - Content rating
#    - Description
#    - Keywords
#    - Screenshots
#    - App Icon

# 6. Pricing and Availability:
#    - Select countries
#    - Set price tier (if paid app)

# 7. App Review Information:
#    - Contact information
#    - Demo account (if required)
#    - Notes for reviewer

# 8. Submit for Review

# Review time: 24-48 hours typically
```

#### Post-Submission

```bash
# Monitor review status
# - In Review
# - Pending Developer Release
# - Ready for Sale
# - Rejected (address issues and resubmit)

# Once approved:
# - Release manually or automatically
# - Announce to users
# - Monitor crash reports and reviews
```

---

## DevOps & Infrastructure

### Step 1: Docker Compose Production Setup

```bash
# Navigate to project root
cd /home/ubuntu/Casper-Code

# Create production docker-compose override
cat > docker-compose.prod.yml << 'EOF'
version: '3.8'

services:
  postgres:
    image: postgres:15
    container_name: caspervpn-db
    restart: unless-stopped
    environment:
      - POSTGRES_USER=${POSTGRES_USER}
      - POSTGRES_PASSWORD=${POSTGRES_PASSWORD}
      - POSTGRES_DB=${POSTGRES_DB}
    volumes:
      - postgres-data:/var/lib/postgresql/data
      - ./backups:/backups
    ports:
      - "127.0.0.1:5432:5432"
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${POSTGRES_USER}"]
      interval: 10s
      timeout: 5s
      retries: 5

  backend-api:
    image: caspervpn/backend-api:latest
    container_name: caspervpn-api
    restart: unless-stopped
    depends_on:
      - postgres
    environment:
      - ConnectionStrings__DefaultConnection=Host=postgres;Port=5432;Database=${POSTGRES_DB};Username=${POSTGRES_USER};Password=${POSTGRES_PASSWORD}
    ports:
      - "127.0.0.1:5000:5000"
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:5000/health"]
      interval: 30s
      timeout: 10s
      retries: 3

  admin-panel:
    image: caspervpn/admin-panel:latest
    container_name: caspervpn-admin
    restart: unless-stopped
    ports:
      - "127.0.0.1:3000:80"

  prometheus:
    image: prom/prometheus:latest
    container_name: caspervpn-prometheus
    restart: unless-stopped
    volumes:
      - ./monitoring/prometheus:/etc/prometheus
      - prometheus-data:/prometheus
    ports:
      - "127.0.0.1:9090:9090"
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'
      - '--storage.tsdb.retention.time=30d'

  grafana:
    image: grafana/grafana:latest
    container_name: caspervpn-grafana
    restart: unless-stopped
    depends_on:
      - prometheus
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=${GRAFANA_PASSWORD}
      - GF_SERVER_ROOT_URL=https://grafana.caspervpn.com
    volumes:
      - ./monitoring/grafana:/etc/grafana/provisioning
      - grafana-data:/var/lib/grafana
    ports:
      - "127.0.0.1:3001:3000"

  alertmanager:
    image: prom/alertmanager:latest
    container_name: caspervpn-alertmanager
    restart: unless-stopped
    volumes:
      - ./monitoring/alertmanager:/etc/alertmanager
    ports:
      - "127.0.0.1:9093:9093"

volumes:
  postgres-data:
  prometheus-data:
  grafana-data:

networks:
  default:
    name: caspervpn-network
EOF

# Start all services
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Check all services
docker compose ps

# View logs
docker compose logs -f
```

### Step 2: Monitoring Setup

#### Configure Prometheus

```yaml
# Edit: monitoring/prometheus/prometheus.yml

global:
  scrape_interval: 15s
  evaluation_interval: 15s

# Alertmanager configuration
alerting:
  alertmanagers:
    - static_configs:
        - targets: ['alertmanager:9093']

# Load rules
rule_files:
  - 'alerts.yml'

# Scrape configurations
scrape_configs:
  # CasperVPN API
  - job_name: 'caspervpn-api'
    static_configs:
      - targets: ['backend-api:5000']
    metrics_path: '/metrics'

  # PostgreSQL
  - job_name: 'postgres'
    static_configs:
      - targets: ['postgres-exporter:9187']

  # Node Exporter (host metrics)
  - job_name: 'node'
    static_configs:
      - targets: ['node-exporter:9100']

  # Docker metrics
  - job_name: 'docker'
    static_configs:
      - targets: ['cadvisor:8080']
```

#### Configure Alert Rules

```yaml
# Edit: monitoring/prometheus/alerts.yml

groups:
  - name: caspervpn_alerts
    interval: 30s
    rules:
      # API is down
      - alert: APIDown
        expr: up{job="caspervpn-api"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "CasperVPN API is down"
          description: "API has been down for more than 1 minute"

      # High error rate
      - alert: HighErrorRate
        expr: rate(http_requests_total{status=~"5.."}[5m]) > 0.05
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High error rate detected"
          description: "Error rate is {{ $value }} errors/sec"

      # High response time
      - alert: HighResponseTime
        expr: histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m])) > 1
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High API response time"
          description: "95th percentile response time is {{ $value }}s"

      # Database connection issues
      - alert: DatabaseConnectionIssues
        expr: up{job="postgres"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "PostgreSQL is down"
          description: "Database has been down for more than 1 minute"

      # High CPU usage
      - alert: HighCPUUsage
        expr: 100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100) > 80
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High CPU usage"
          description: "CPU usage is above 80% (current: {{ $value }}%)"

      # High memory usage
      - alert: HighMemoryUsage
        expr: (1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100 > 90
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "High memory usage"
          description: "Memory usage is above 90% (current: {{ $value }}%)"

      # Disk space low
      - alert: DiskSpaceLow
        expr: (1 - (node_filesystem_avail_bytes / node_filesystem_size_bytes)) * 100 > 80
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "Disk space low"
          description: "Disk usage is above 80% (current: {{ $value }}%)"
```

#### Configure Alertmanager

```yaml
# Edit: monitoring/alertmanager/alertmanager.yml

global:
  resolve_timeout: 5m
  smtp_smarthost: 'smtp.sendgrid.net:587'
  smtp_from: 'alerts@caspervpn.com'
  smtp_auth_username: 'apikey'
  smtp_auth_password: '${SENDGRID_API_KEY}'

route:
  group_by: ['alertname', 'cluster', 'service']
  group_wait: 10s
  group_interval: 10s
  repeat_interval: 12h
  receiver: 'email-notifications'
  routes:
    - match:
        severity: critical
      receiver: 'pagerduty'
      continue: true
    - match:
        severity: warning
      receiver: 'email-notifications'

receivers:
  - name: 'email-notifications'
    email_configs:
      - to: 'devops@caspervpn.com'
        headers:
          Subject: '[CasperVPN] {{ .GroupLabels.alertname }}'
        html: |
          <h2>Alert: {{ .GroupLabels.alertname }}</h2>
          <p><strong>Summary:</strong> {{ .CommonAnnotations.summary }}</p>
          <p><strong>Description:</strong> {{ .CommonAnnotations.description }}</p>
          <p><strong>Severity:</strong> {{ .CommonLabels.severity }}</p>
          <p><strong>Time:</strong> {{ .StartsAt }}</p>

  - name: 'pagerduty'
    pagerduty_configs:
      - service_key: '${PAGERDUTY_SERVICE_KEY}'
        description: '{{ .CommonAnnotations.summary }}'

  - name: 'slack'
    slack_configs:
      - api_url: '${SLACK_WEBHOOK_URL}'
        channel: '#alerts'
        title: '{{ .GroupLabels.alertname }}'
        text: '{{ .CommonAnnotations.description }}'
```

#### Access Grafana

```bash
# Open Grafana in browser
# https://grafana.caspervpn.com
# or
# http://your-server-ip:3001

# Default credentials:
# Username: admin
# Password: (set in .env GRAFANA_PASSWORD)

# Import dashboards:
# 1. Go to Dashboards > Import
# 2. Upload JSON files from monitoring/grafana/dashboards/
# 3. Select Prometheus datasource
# 4. Import

# Available dashboards:
# - CasperVPN Overview
# - API Performance
# - Database Metrics
# - System Metrics
```

### Step 3: Backup Configuration

#### Automated Database Backups

```bash
# Create backup script
cat > /home/ubuntu/Casper-Code/scripts/backup-database.sh << 'EOF'
#!/bin/bash

# Configuration
BACKUP_DIR="/home/ubuntu/Casper-Code/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="caspervpn_backup_${TIMESTAMP}.sql"
DB_NAME="caspervpn"
DB_USER="caspervpn_user"
DB_PASSWORD="your-password"

# Create backup directory if not exists
mkdir -p ${BACKUP_DIR}

# Perform backup
PGPASSWORD=${DB_PASSWORD} pg_dump -h localhost -U ${DB_USER} -d ${DB_NAME} -F c -b -v -f "${BACKUP_DIR}/${BACKUP_FILE}"

# Compress backup
gzip "${BACKUP_DIR}/${BACKUP_FILE}"

# Keep only last 30 days of backups
find ${BACKUP_DIR} -name "caspervpn_backup_*.sql.gz" -mtime +30 -delete

# Upload to cloud storage (optional)
# aws s3 cp "${BACKUP_DIR}/${BACKUP_FILE}.gz" s3://caspervpn-backups/

echo "Backup completed: ${BACKUP_FILE}.gz"
EOF

# Make executable
chmod +x /home/ubuntu/Casper-Code/scripts/backup-database.sh

# Test backup
/home/ubuntu/Casper-Code/scripts/backup-database.sh

# Schedule daily backups (3 AM)
crontab -e

# Add this line:
# 0 3 * * * /home/ubuntu/Casper-Code/scripts/backup-database.sh >> /var/log/caspervpn-backup.log 2>&1
```

#### Backup Restore Procedure

```bash
# List available backups
ls -lh /home/ubuntu/Casper-Code/backups/

# Restore from backup
BACKUP_FILE="caspervpn_backup_20251209_030000.sql.gz"

# Decompress
gunzip /home/ubuntu/Casper-Code/backups/${BACKUP_FILE}

# Stop API to prevent writes
docker compose stop backend-api

# Restore database
PGPASSWORD=your-password pg_restore -h localhost -U caspervpn_user -d caspervpn -c -v /home/ubuntu/Casper-Code/backups/${BACKUP_FILE%.gz}

# Start API
docker compose start backend-api

# Verify
curl https://api.caspervpn.com/health
```

---

## Post-Deployment Verification

### Backend API Checks

```bash
# 1. Health endpoint
curl https://api.caspervpn.com/health
# Expected: {"status":"Healthy","timestamp":"..."}

# 2. API status
curl https://api.caspervpn.com/api/api/status
# Expected: {"status":"Online","version":"1.0.0","timestamp":"..."}

# 3. Server list
curl https://api.caspervpn.com/api/api/servers
# Expected: JSON array of servers

# 4. Login (should fail with invalid credentials)
curl -X POST https://api.caspervpn.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@caspervpn.com","password":"WrongPassword"}'
# Expected: 401 Unauthorized

# 5. Login (should succeed with correct credentials)
curl -X POST https://api.caspervpn.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@caspervpn.com","password":"Admin@123"}'
# Expected: {"token":"...", "refreshToken":"...", "user":{...}}

# 6. Swagger documentation
# Open in browser: https://api.caspervpn.com/swagger

# 7. Performance test
ab -n 1000 -c 10 https://api.caspervpn.com/health
```

### Admin Panel Checks

```bash
# 1. Admin panel loads
curl -I https://admin.caspervpn.com
# Expected: HTTP/2 200

# 2. Static assets load
curl -I https://admin.caspervpn.com/static/css/main.css
# Expected: HTTP/2 200

# 3. Browser checks:
# - Open https://admin.caspervpn.com
# - Login with admin@caspervpn.com / Admin@123
# - Navigate to each page:
#   ✓ Dashboard
#   ✓ Users
#   ✓ Servers
#   ✓ Subscriptions
#   ✓ Payments
#   ✓ Analytics
#   ✓ Settings
# - Check for JavaScript errors in console
# - Verify API calls succeed
```

### Infrastructure Checks

```bash
# 1. Docker containers running
docker compose ps
# All services should show "Up" status

# 2. PostgreSQL connection
psql -h localhost -U caspervpn_user -d caspervpn -c "SELECT COUNT(*) FROM users;"
# Expected: Count of users

# 3. Nginx status
sudo systemctl status nginx
# Expected: active (running)

# 4. SSL certificates
sudo certbot certificates
# Expected: Valid certificates for api.caspervpn.com and admin.caspervpn.com

# 5. Disk space
df -h
# Ensure sufficient free space (> 20% free)

# 6. Memory usage
free -h
# Ensure sufficient free memory

# 7. CPU usage
top
# Check for normal CPU usage

# 8. Network connectivity
ping -c 4 8.8.8.8
# Expected: 0% packet loss
```

### Monitoring Checks

```bash
# 1. Prometheus targets
# Open: https://your-server-ip:9090/targets
# All targets should be "UP"

# 2. Grafana dashboards
# Open: https://your-server-ip:3001
# Login with Grafana credentials
# Verify dashboards display data

# 3. Alertmanager
# Open: https://your-server-ip:9093
# Verify no active alerts (or expected alerts)

# 4. Test alert
# Stop backend API temporarily
docker compose stop backend-api
# Wait 1-2 minutes
# Check Alertmanager for "APIDown" alert
# Check email for alert notification
# Start backend API
docker compose start backend-api
```

---

## Rollback Procedures

### Backend API Rollback

```bash
# 1. Identify last working version
docker images | grep caspervpn/backend-api

# 2. Stop current version
docker compose stop backend-api

# 3. Update docker-compose.yml
# Change image tag to previous version
# backend-api:
#   image: caspervpn/backend-api:v1.0.0  # Previous version

# 4. Start previous version
docker compose up -d backend-api

# 5. Verify
curl https://api.caspervpn.com/health
docker compose logs -f backend-api

# 6. Database rollback (if needed)
# See "Backup Restore Procedure" section
```

### Admin Panel Rollback

```bash
# Using Docker:
# 1. Stop current version
docker compose stop admin-panel

# 2. Update docker-compose.yml with previous version
# admin-panel:
#   image: caspervpn/admin-panel:v1.0.0

# 3. Start previous version
docker compose up -d admin-panel

# Using static files:
# 1. Restore from backup
sudo cp -r /var/www/admin.caspervpn.com.backup/* /var/www/admin.caspervpn.com/

# 2. Verify
curl -I https://admin.caspervpn.com
```

### Database Rollback

```bash
# See "Backup Restore Procedure" section

# Quick rollback:
# 1. Identify backup
ls -lh /home/ubuntu/Casper-Code/backups/

# 2. Restore
./scripts/restore-database.sh caspervpn_backup_20251209_030000.sql.gz

# 3. Restart API
docker compose restart backend-api
```

---

## Troubleshooting

### Common Issues

#### 1. API Not Responding

**Symptoms:**
- `curl https://api.caspervpn.com/health` returns connection error
- 502 Bad Gateway from Nginx

**Solutions:**

```bash
# Check if API container is running
docker compose ps backend-api

# If not running, start it
docker compose up -d backend-api

# Check logs for errors
docker compose logs backend-api

# Check database connection
docker compose exec postgres psql -U caspervpn_user -d caspervpn -c "SELECT 1;"

# Restart API
docker compose restart backend-api

# Check Nginx error logs
sudo tail -f /var/log/nginx/api.caspervpn.com.error.log
```

#### 2. Database Connection Failed

**Symptoms:**
- API logs show "Failed to connect to database"
- Error: "FATAL: password authentication failed"

**Solutions:**

```bash
# Verify PostgreSQL is running
docker compose ps postgres

# Check PostgreSQL logs
docker compose logs postgres

# Verify credentials
# Check .env file
cat .env | grep POSTGRES

# Test connection manually
docker compose exec postgres psql -U caspervpn_user -d caspervpn

# If password is wrong, update it:
# 1. Stop API
docker compose stop backend-api

# 2. Update password in PostgreSQL
docker compose exec postgres psql -U postgres -c "ALTER USER caspervpn_user WITH PASSWORD 'new-password';"

# 3. Update .env file
# Update POSTGRES_PASSWORD

# 4. Restart services
docker compose restart backend-api postgres
```

#### 3. Admin Panel Blank Page

**Symptoms:**
- Admin panel loads but shows blank page
- Browser console shows errors

**Solutions:**

```bash
# Check browser console for errors
# Common issues:
# - API_URL not set correctly
# - CORS errors
# - JavaScript errors

# Verify environment variables
# Edit admin-panel-react/.env
# Ensure REACT_APP_API_URL is correct

# Rebuild admin panel
cd admin-panel-react
npm run build

# Redeploy
sudo cp -r build/* /var/www/admin.caspervpn.com/

# Clear browser cache
# Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)

# Check Nginx logs
sudo tail -f /var/log/nginx/admin.caspervpn.com.error.log
```

#### 4. iOS App Connection Failed

**Symptoms:**
- iOS app shows "Connection failed" error
- VPN won't connect

**Solutions:**

```bash
# 1. Verify API is accessible from iOS device
# On iOS device, open Safari and go to:
# https://api.caspervpn.com/health

# 2. Check API endpoint in app
# Verify Config.swift has correct API URL

# 3. Check VPN entitlements
# In Xcode, verify:
# - Network Extensions capability enabled
# - Personal VPN capability enabled
# - VPN configuration profile installed

# 4. Check logs in Xcode
# Window > Devices and Simulators
# Select device > View Device Logs
# Filter by "CasperVPN"

# 5. Test with different server
# Try connecting to a different VPN server

# 6. Reinstall app
# Delete app from device
# Rebuild and install from Xcode
```

#### 5. High CPU Usage

**Symptoms:**
- Server becomes slow
- `top` shows high CPU usage
- API response times increase

**Solutions:**

```bash
# Identify the process
top
# or
htop

# If API is using high CPU:
# 1. Check for runaway requests
docker compose logs backend-api | grep ERROR

# 2. Restart API
docker compose restart backend-api

# 3. Check database queries
# Enable query logging in PostgreSQL
docker compose exec postgres psql -U postgres -c "ALTER SYSTEM SET log_statement = 'all';"
docker compose restart postgres

# 4. Scale horizontally (add more instances)
# Update docker-compose.yml to run multiple API instances
# Configure Nginx load balancing

# If PostgreSQL is using high CPU:
# 1. Check slow queries
docker compose exec postgres psql -U caspervpn_user -d caspervpn -c "
  SELECT query, calls, total_time, mean_time 
  FROM pg_stat_statements 
  ORDER BY mean_time DESC 
  LIMIT 10;
"

# 2. Add indexes to slow queries
# 3. Optimize queries
# 4. Increase shared_buffers in postgresql.conf
```

#### 6. Disk Space Full

**Symptoms:**
- Services fail to start
- "No space left on device" errors
- Logs show disk full

**Solutions:**

```bash
# Check disk usage
df -h

# Find largest directories
du -sh /* | sort -h

# Clean Docker
docker system prune -a --volumes

# Clean logs
sudo journalctl --vacuum-time=7d
sudo find /var/log -type f -name "*.log" -mtime +30 -delete

# Clean old backups
find /home/ubuntu/Casper-Code/backups -name "*.sql.gz" -mtime +30 -delete

# Resize disk (if on cloud)
# AWS: Modify EBS volume size
# DigitalOcean: Resize droplet
# Then extend filesystem:
sudo resize2fs /dev/sda1
```

#### 7. SSL Certificate Expired

**Symptoms:**
- Browser shows "Your connection is not private"
- SSL error in logs
- API/Admin panel inaccessible

**Solutions:**

```bash
# Check certificate expiration
sudo certbot certificates

# Renew certificate
sudo certbot renew

# If renewal fails, force renew
sudo certbot renew --force-renewal

# Restart Nginx
sudo systemctl restart nginx

# Test renewal process
sudo certbot renew --dry-run

# Check auto-renewal timer
sudo systemctl status certbot.timer

# Enable auto-renewal if disabled
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

---

## Production Checklist

### Security Checklist

- [ ] **Passwords Changed**
  - [ ] Database password
  - [ ] Admin user password
  - [ ] Grafana password
  - [ ] JWT secret key

- [ ] **SSL/TLS Configured**
  - [ ] API domain has valid certificate
  - [ ] Admin domain has valid certificate
  - [ ] Auto-renewal enabled
  - [ ] Strong cipher suites configured

- [ ] **Firewall Configured**
  - [ ] Only ports 80, 443, 22 open
  - [ ] Database port (5432) only accessible from localhost
  - [ ] Monitoring ports only accessible from localhost

- [ ] **Environment Variables**
  - [ ] All secrets in .env files
  - [ ] .env files not committed to Git
  - [ ] Production API URLs configured

- [ ] **Database Security**
  - [ ] Strong database password
  - [ ] Database only accessible from localhost
  - [ ] Regular backups configured
  - [ ] Backup encryption enabled

- [ ] **API Security**
  - [ ] CORS configured correctly
  - [ ] Rate limiting enabled
  - [ ] JWT expiry set appropriately
  - [ ] Input validation enabled

- [ ] **Server Hardening**
  - [ ] SSH key-only authentication
  - [ ] Disable root SSH login
  - [ ] Fail2ban installed
  - [ ] Automatic security updates enabled

### Performance Checklist

- [ ] **Database Optimization**
  - [ ] Indexes created on frequently queried columns
  - [ ] Query performance tested
  - [ ] Connection pooling configured
  - [ ] Slow query logging enabled

- [ ] **Caching**
  - [ ] Redis/Memcached configured (if applicable)
  - [ ] API response caching enabled
  - [ ] Static asset caching configured
  - [ ] Browser caching headers set

- [ ] **CDN Configuration** (Optional)
  - [ ] Static assets served via CDN
  - [ ] API behind CDN (if applicable)
  - [ ] CDN cache invalidation configured

- [ ] **Load Balancing** (Optional, for high traffic)
  - [ ] Multiple API instances running
  - [ ] Nginx load balancing configured
  - [ ] Health checks configured
  - [ ] Session affinity configured (if needed)

### Monitoring Checklist

- [ ] **Prometheus**
  - [ ] All services being scraped
  - [ ] Retention policy configured
  - [ ] Storage sized appropriately

- [ ] **Grafana**
  - [ ] Dashboards imported
  - [ ] Datasources configured
  - [ ] Alerts configured
  - [ ] Users and permissions set

- [ ] **Alertmanager**
  - [ ] Email notifications configured
  - [ ] PagerDuty/Slack configured (if applicable)
  - [ ] Alert rules tested
  - [ ] On-call schedule defined

- [ ] **Logging**
  - [ ] Centralized logging configured
  - [ ] Log retention policy set
  - [ ] Log rotation enabled
  - [ ] Error tracking (Sentry) configured (optional)

### Backup Checklist

- [ ] **Database Backups**
  - [ ] Daily backups scheduled
  - [ ] Backups tested (restore test)
  - [ ] Off-site backup storage configured
  - [ ] Backup retention policy set (30 days)

- [ ] **Application Backups**
  - [ ] Configuration files backed up
  - [ ] .env files backed up securely
  - [ ] SSL certificates backed up
  - [ ] Docker images pushed to registry

- [ ] **Restore Procedures**
  - [ ] Restore procedure documented
  - [ ] Restore procedure tested
  - [ ] Recovery Time Objective (RTO) defined
  - [ ] Recovery Point Objective (RPO) defined

### Documentation Checklist

- [ ] **Technical Documentation**
  - [ ] Architecture diagram
  - [ ] API documentation (Swagger)
  - [ ] Database schema documented
  - [ ] Deployment guide (this document)

- [ ] **Operational Documentation**
  - [ ] Runbook for common issues
  - [ ] On-call procedures
  - [ ] Escalation path defined
  - [ ] Contact information updated

- [ ] **User Documentation**
  - [ ] Admin panel user guide
  - [ ] iOS app user guide
  - [ ] FAQ document
  - [ ] Support procedures

### Testing Checklist

- [ ] **API Testing**
  - [ ] All 46 endpoints tested
  - [ ] Authentication flow tested
  - [ ] Authorization tested
  - [ ] Error handling tested
  - [ ] Load testing completed

- [ ] **Admin Panel Testing**
  - [ ] All 8 pages tested
  - [ ] CRUD operations tested
  - [ ] Charts display correctly
  - [ ] Mobile responsive
  - [ ] Cross-browser tested

- [ ] **iOS App Testing**
  - [ ] Login/logout tested
  - [ ] VPN connection tested
  - [ ] Kill switch tested
  - [ ] Auto-reconnect tested
  - [ ] All iOS versions tested
  - [ ] TestFlight beta tested

- [ ] **Integration Testing**
  - [ ] End-to-end user flows tested
  - [ ] Payment flow tested (Stripe)
  - [ ] Email notifications tested
  - [ ] API → Admin Panel integration tested
  - [ ] API → iOS App integration tested

---

## Monitoring & Maintenance

### Daily Tasks

```bash
# Check service status
docker compose ps

# Check disk space
df -h

# Check logs for errors
docker compose logs --tail=100 | grep -i error

# Check monitoring alerts
# Visit https://your-server-ip:9093
```

### Weekly Tasks

```bash
# Review backup logs
cat /var/log/caspervpn-backup.log

# Test backup restore
# Restore to test database and verify

# Review security logs
sudo journalctl -u ssh --since "1 week ago" | grep "Failed password"

# Review performance metrics
# Check Grafana dashboards

# Update dependencies (if needed)
# Check for security updates
sudo apt update
sudo apt list --upgradable
```

### Monthly Tasks

```bash
# Update system packages
sudo apt update
sudo apt upgrade -y

# Rotate logs
sudo logrotate -f /etc/logrotate.conf

# Review and optimize database
docker compose exec postgres psql -U caspervpn_user -d caspervpn -c "VACUUM ANALYZE;"

# Review disk usage
du -sh /var/lib/docker
du -sh /home/ubuntu/Casper-Code/backups

# Review SSL certificates
sudo certbot certificates

# Security audit
# Run security scanning tools
# Review access logs
```

### Quarterly Tasks

```bash
# Disaster recovery drill
# Simulate server failure
# Restore from backups
# Document recovery time

# Performance optimization review
# Analyze slow queries
# Review API response times
# Optimize indexes

# Capacity planning
# Review growth metrics
# Plan infrastructure scaling
# Budget for next quarter

# Security review
# Update passwords
# Review access logs
# Check for vulnerabilities
```

---

## Deployment Timeline

### Pre-Deployment (Day -7 to -1)

| Day | Task | Duration | Owner |
|-----|------|----------|-------|
| -7 | Server provisioning | 2 hours | DevOps |
| -7 | Domain & DNS setup | 1 hour | DevOps |
| -6 | Repository access setup | 1 hour | Dev Team |
| -6 | Environment variables configuration | 2 hours | DevOps |
| -5 | Backend API build & test | 4 hours | Backend Dev |
| -5 | Admin Panel build & test | 4 hours | Frontend Dev |
| -4 | Database setup & migrations | 2 hours | Backend Dev |
| -4 | SSL certificates setup | 1 hour | DevOps |
| -3 | Nginx configuration | 2 hours | DevOps |
| -3 | Monitoring setup | 3 hours | DevOps |
| -2 | iOS app TestFlight setup | 4 hours | iOS Dev |
| -2 | Integration testing | 6 hours | QA Team |
| -1 | Final review & dry run | 4 hours | All Team |
| -1 | Documentation review | 2 hours | Tech Writer |

### Deployment Day (Day 0)

| Time | Task | Duration | Owner |
|------|------|----------|-------|
| 09:00 | Team standup | 15 min | All |
| 09:15 | Deploy database | 30 min | DevOps |
| 09:45 | Run migrations | 15 min | Backend Dev |
| 10:00 | Deploy Backend API | 30 min | DevOps |
| 10:30 | Verify API endpoints | 30 min | Backend Dev |
| 11:00 | Deploy Admin Panel | 30 min | DevOps |
| 11:30 | Verify Admin Panel | 30 min | Frontend Dev |
| 12:00 | Lunch break | 60 min | All |
| 13:00 | Configure monitoring | 30 min | DevOps |
| 13:30 | Upload iOS to TestFlight | 45 min | iOS Dev |
| 14:15 | End-to-end testing | 90 min | QA Team |
| 15:45 | Fix critical issues | 60 min | Dev Team |
| 16:45 | Final verification | 30 min | All |
| 17:15 | Go/No-Go decision | 15 min | Team Lead |
| 17:30 | Public announcement | 30 min | Marketing |
| 18:00 | Deployment complete! | - | All |

### Post-Deployment (Day +1 to +7)

| Day | Task | Duration | Owner |
|-----|------|----------|-------|
| +1 | Monitor logs & metrics | Ongoing | DevOps |
| +1 | Address user feedback | Ongoing | Support |
| +1 | Bug fixes (if any) | As needed | Dev Team |
| +2 | Performance review | 2 hours | DevOps |
| +3 | User analytics review | 1 hour | Product |
| +7 | Week 1 retrospective | 2 hours | All Team |

---

## Support Contacts

### Technical Support

- **DevOps Team:** devops@caspervpn.com
- **Backend Team:** backend@caspervpn.com
- **Frontend Team:** frontend@caspervpn.com
- **iOS Team:** ios@caspervpn.com
- **QA Team:** qa@caspervpn.com

### Escalation Path

1. **Level 1:** On-call engineer (via PagerDuty)
2. **Level 2:** Team lead
3. **Level 3:** CTO / Engineering Manager
4. **Level 4:** CEO (critical outages only)

### External Vendors

- **Cloud Provider:** [AWS / DigitalOcean / etc.]
- **DNS Provider:** [Cloudflare / Route53 / etc.]
- **Email Service:** SendGrid (support@sendgrid.com)
- **Payment Processor:** Stripe (support@stripe.com)

---

## Appendix

### A. Environment Variables Reference

#### Backend API (.env)
```bash
# Database
ConnectionStrings__DefaultConnection=Host=postgres;Port=5432;Database=caspervpn;Username=caspervpn_user;Password=<password>

# JWT
Jwt__Secret=<256-bit-secret>
Jwt__Issuer=https://api.caspervpn.com
Jwt__Audience=https://api.caspervpn.com
Jwt__ExpiryHours=1
Jwt__RefreshTokenExpiryDays=30

# Stripe
Stripe__SecretKey=sk_live_...
Stripe__PublishableKey=pk_live_...
Stripe__WebhookSecret=whsec_...

# Email (SMTP)
Email__SmtpHost=smtp.sendgrid.net
Email__SmtpPort=587
Email__SmtpUser=apikey
Email__SmtpPassword=<sendgrid-api-key>
Email__FromEmail=noreply@caspervpn.com
Email__FromName=CasperVPN

# FreeRADIUS
FreeRadius__Host=radius.caspervpn.com
FreeRadius__Secret=<radius-secret>
FreeRadius__Port=1812

# App Settings
ASPNETCORE_ENVIRONMENT=Production
ASPNETCORE_URLS=http://+:5000
ALLOWED_ORIGINS=https://admin.caspervpn.com,https://www.caspervpn.com
```

#### Admin Panel (.env)
```bash
REACT_APP_API_URL=https://api.caspervpn.com
REACT_APP_STRIPE_PUBLISHABLE_KEY=pk_live_...
NODE_ENV=production
```

#### Docker Compose (.env)
```bash
# PostgreSQL
POSTGRES_USER=caspervpn_user
POSTGRES_PASSWORD=<secure-password>
POSTGRES_DB=caspervpn

# Ports
API_PORT=5000
ADMIN_PORT=3000
PROMETHEUS_PORT=9090
GRAFANA_PORT=3001
ALERTMANAGER_PORT=9093

# Grafana
GRAFANA_PASSWORD=<secure-password>

# Monitoring
SENDGRID_API_KEY=<sendgrid-api-key>
PAGERDUTY_SERVICE_KEY=<pagerduty-key>
SLACK_WEBHOOK_URL=<slack-webhook>
```

### B. Port Reference

| Service | Port | Protocol | Access |
|---------|------|----------|--------|
| HTTP | 80 | TCP | Public |
| HTTPS | 443 | TCP | Public |
| SSH | 22 | TCP | Restricted |
| PostgreSQL | 5432 | TCP | Localhost only |
| Backend API | 5000 | TCP | Localhost only (via Nginx) |
| Admin Panel | 3000 | TCP | Localhost only (via Nginx) |
| Prometheus | 9090 | TCP | Localhost only |
| Grafana | 3001 | TCP | Localhost only |
| Alertmanager | 9093 | TCP | Localhost only |
| WireGuard | 51820 | UDP | Public (VPN servers) |
| FreeRADIUS | 1812 | UDP | VPN servers only |

### C. Useful Commands

```bash
# Docker
docker compose ps                          # List containers
docker compose logs -f <service>           # Follow logs
docker compose restart <service>           # Restart service
docker compose up -d <service>             # Start service
docker compose down                        # Stop all services
docker system prune -a                     # Clean Docker

# PostgreSQL
psql -h localhost -U caspervpn_user -d caspervpn          # Connect to DB
pg_dump -U caspervpn_user caspervpn > backup.sql          # Backup DB
psql -U caspervpn_user caspervpn < backup.sql             # Restore DB

# Nginx
sudo nginx -t                              # Test configuration
sudo systemctl reload nginx                # Reload config
sudo systemctl restart nginx               # Restart Nginx
sudo tail -f /var/log/nginx/error.log     # View error logs

# SSL
sudo certbot certificates                  # List certificates
sudo certbot renew                         # Renew certificates
sudo certbot renew --dry-run              # Test renewal

# System
df -h                                      # Disk usage
free -h                                    # Memory usage
top                                        # Process monitor
htop                                       # Better process monitor
systemctl status <service>                 # Service status
journalctl -u <service> -f                # Service logs

# Firewall
sudo ufw status                            # Firewall status
sudo ufw allow 80/tcp                      # Allow port
sudo ufw deny 3000/tcp                     # Deny port

# Backup
./scripts/backup-database.sh               # Manual backup
./scripts/restore-database.sh <file>       # Manual restore
```

---

**End of Deployment Guide**

*Last Updated: December 9, 2025*  
*Version: 1.0*  
*Maintained by: CasperVPN DevOps Team*

For questions or support, contact: devops@caspervpn.com
