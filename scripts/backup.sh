#!/bin/bash

# ============================================
# CasperVPN Backup Script
# ============================================
# Backup database and important data

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Backup Script${NC}"
echo -e "${GREEN}============================================${NC}"

# Load environment variables
if [ -f ".env" ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Create backup directory
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}Creating backup in $BACKUP_DIR${NC}"

# Backup PostgreSQL database
echo -e "${YELLOW}Backing up PostgreSQL database...${NC}"
docker-compose exec -T postgres pg_dump -U "${DB_USER:-casperuser}" "${DB_NAME:-caspervpn}" | gzip > "$BACKUP_DIR/database.sql.gz"
echo -e "${GREEN}✓ Database backup complete${NC}"

# Backup Redis data
echo -e "${YELLOW}Backing up Redis data...${NC}"
docker-compose exec -T redis redis-cli --raw BGSAVE >/dev/null 2>&1 || true
sleep 2
docker cp caspervpn-redis:/data/dump.rdb "$BACKUP_DIR/redis-dump.rdb" 2>/dev/null || echo -e "${YELLOW}Redis backup skipped${NC}"

# Backup environment files
echo -e "${YELLOW}Backing up configuration files...${NC}"
cp .env "$BACKUP_DIR/.env.backup" 2>/dev/null || true
cp -r config "$BACKUP_DIR/config" 2>/dev/null || true
echo -e "${GREEN}✓ Configuration backup complete${NC}"

# Backup nginx configuration
echo -e "${YELLOW}Backing up nginx configuration...${NC}"
cp -r nginx "$BACKUP_DIR/nginx" 2>/dev/null || true
echo -e "${GREEN}✓ Nginx configuration backup complete${NC}"

# Create backup metadata
cat > "$BACKUP_DIR/metadata.txt" << EOF
Backup Date: $(date)
Database: ${DB_NAME:-caspervpn}
Environment: ${ENVIRONMENT:-production}
Backup Version: 1.0
EOF

# Compress backup
echo -e "${YELLOW}Compressing backup...${NC}"
tar -czf "$BACKUP_DIR.tar.gz" -C backups "$(basename $BACKUP_DIR)"
rm -rf "$BACKUP_DIR"
echo -e "${GREEN}✓ Backup compressed${NC}"

# Upload to S3 (if configured)
if [ "${BACKUP_ENABLED:-false}" = "true" ] && command -v aws >/dev/null 2>&1; then
    echo -e "${YELLOW}Uploading backup to S3...${NC}"
    aws s3 cp "$BACKUP_DIR.tar.gz" "s3://${BACKUP_S3_BUCKET}/" || echo -e "${YELLOW}S3 upload failed${NC}"
    echo -e "${GREEN}✓ Backup uploaded to S3${NC}"
fi

# Clean old backups
echo -e "${YELLOW}Cleaning old backups...${NC}"
find backups -name "*.tar.gz" -mtime +${BACKUP_RETENTION_DAYS:-7} -delete 2>/dev/null || true
echo -e "${GREEN}✓ Old backups cleaned${NC}"

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Backup Complete!${NC}"
echo -e "${GREEN}Backup file: $BACKUP_DIR.tar.gz${NC}"
echo -e "${GREEN}============================================${NC}"
