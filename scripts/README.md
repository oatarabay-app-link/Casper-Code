# CasperVPN Helper Scripts

This directory contains helper scripts for managing CasperVPN services.

## Available Scripts

### setup.sh
**Purpose:** Initial setup of the CasperVPN environment

**Usage:**
```bash
./scripts/setup.sh
```

**What it does:**
- Checks for required dependencies (Docker, Docker Compose)
- Creates .env file from template
- Generates SSL certificates for local development
- Builds Docker images
- Initializes database
- Adds hosts entries

---

### deploy.sh
**Purpose:** Deploy CasperVPN services to different environments

**Usage:**
```bash
./scripts/deploy.sh [environment]

# Examples:
./scripts/deploy.sh dev         # Development environment
./scripts/deploy.sh staging     # Staging environment
./scripts/deploy.sh production  # Production environment
```

**What it does:**
- Loads environment-specific configuration
- Pulls latest Docker images (for non-dev)
- Stops existing services
- Starts services with appropriate compose file
- Runs health checks

---

### backup.sh
**Purpose:** Create backups of database and configuration

**Usage:**
```bash
./scripts/backup.sh
```

**What it does:**
- Backs up PostgreSQL database
- Backs up Redis data
- Backs up configuration files
- Compresses backup
- Uploads to S3 (if configured)
- Cleans old backups

**Backups are stored in:** `backups/YYYYMMDD_HHMMSS.tar.gz`

---

### restore.sh
**Purpose:** Restore from a backup

**Usage:**
```bash
# List available backups
./scripts/restore.sh

# Restore from specific backup
./scripts/restore.sh backups/20231205_143000.tar.gz
```

**What it does:**
- Extracts backup file
- Restores PostgreSQL database
- Restores Redis data
- Provides information about configuration files

---

### logs.sh
**Purpose:** View logs from CasperVPN services

**Usage:**
```bash
# View all logs
./scripts/logs.sh all

# View specific service logs
./scripts/logs.sh api
./scripts/logs.sh admin-react
./scripts/logs.sh server-agent

# Follow logs in real-time
./scripts/logs.sh api -f

# View last 100 lines
./scripts/logs.sh api --tail=100
```

**Available services:**
- `api` - .NET Core API Backend
- `admin-react` - React Admin Panel
- `admin-php` - PHP Laravel Admin Panel
- `server-agent` - Rust Server Agent
- `postgres` - PostgreSQL Database
- `redis` - Redis Cache
- `nginx` - Nginx Reverse Proxy

---

### health-check.sh
**Purpose:** Check health status of all services

**Usage:**
```bash
./scripts/health-check.sh
```

**What it checks:**
- Docker container status
- HTTP endpoint health
- Database connections
- Resource usage (CPU, Memory)

---

## Making Scripts Executable

After cloning the repository, make sure to make the scripts executable:

```bash
chmod +x scripts/*.sh
```

## Troubleshooting

If scripts fail to execute:

1. Check file permissions:
   ```bash
   ls -la scripts/
   ```

2. Ensure line endings are Unix-style (LF, not CRLF):
   ```bash
   dos2unix scripts/*.sh  # If available
   ```

3. Check Docker and Docker Compose are installed:
   ```bash
   docker --version
   docker-compose --version
   ```

4. Ensure you're in the project root directory

5. Check logs for specific errors:
   ```bash
   ./scripts/logs.sh <service>
   ```
