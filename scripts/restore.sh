#!/bin/bash

# ============================================
# CasperVPN Restore Script
# ============================================
# Restore database and configuration from backup

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Restore Script${NC}"
echo -e "${GREEN}============================================${NC}"

# Check for backup file
if [ -z "$1" ]; then
    echo -e "${YELLOW}Available backups:${NC}"
    ls -lh backups/*.tar.gz 2>/dev/null || echo "No backups found"
    echo -e ""
    echo -e "Usage: $0 <backup-file.tar.gz>"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}Backup file not found: $BACKUP_FILE${NC}"
    exit 1
fi

echo -e "${YELLOW}Restoring from: $BACKUP_FILE${NC}"
echo -e "${RED}⚠ WARNING: This will overwrite existing data!${NC}"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo -e "${YELLOW}Restore cancelled${NC}"
    exit 0
fi

# Load environment variables
if [ -f ".env" ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Extract backup
echo -e "${YELLOW}Extracting backup...${NC}"
BACKUP_DIR=$(basename "$BACKUP_FILE" .tar.gz)
tar -xzf "$BACKUP_FILE" -C backups
echo -e "${GREEN}✓ Backup extracted${NC}"

# Restore database
if [ -f "backups/$BACKUP_DIR/database.sql.gz" ]; then
    echo -e "${YELLOW}Restoring database...${NC}"
    gunzip < "backups/$BACKUP_DIR/database.sql.gz" | docker-compose exec -T postgres psql -U "${DB_USER:-casperuser}" "${DB_NAME:-caspervpn}"
    echo -e "${GREEN}✓ Database restored${NC}"
else
    echo -e "${RED}Database backup not found${NC}"
fi

# Restore Redis data
if [ -f "backups/$BACKUP_DIR/redis-dump.rdb" ]; then
    echo -e "${YELLOW}Restoring Redis data...${NC}"
    docker-compose stop redis
    docker cp "backups/$BACKUP_DIR/redis-dump.rdb" caspervpn-redis:/data/dump.rdb
    docker-compose start redis
    echo -e "${GREEN}✓ Redis data restored${NC}"
fi

# Restore configuration
if [ -f "backups/$BACKUP_DIR/.env.backup" ]; then
    echo -e "${YELLOW}Configuration files found in backup${NC}"
    echo -e "${YELLOW}To restore, manually copy from: backups/$BACKUP_DIR/${NC}"
fi

# Clean up
rm -rf "backups/$BACKUP_DIR"

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Restore Complete!${NC}"
echo -e "${GREEN}============================================${NC}"
echo -e ""
echo -e "${YELLOW}Restart services with: ./scripts/deploy.sh${NC}"
echo -e ""
