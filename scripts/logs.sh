#!/bin/bash

# ============================================
# CasperVPN Logs Script
# ============================================
# View logs from CasperVPN services

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

SERVICE="$1"
FOLLOW="${2:--f}"

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Logs${NC}"
echo -e "${GREEN}============================================${NC}"

if [ -z "$SERVICE" ]; then
    echo -e "${YELLOW}Available services:${NC}"
    docker-compose ps --services
    echo -e ""
    echo -e "Usage: $0 <service> [options]"
    echo -e "  Options: -f (follow), --tail=N (last N lines)"
    echo -e ""
    echo -e "Examples:"
    echo -e "  $0 api              # Show all API logs"
    echo -e "  $0 api -f           # Follow API logs"
    echo -e "  $0 api --tail=100   # Show last 100 lines"
    echo -e "  $0 all              # Show all service logs"
    echo -e ""
    exit 1
fi

if [ "$SERVICE" = "all" ]; then
    echo -e "${YELLOW}Showing logs for all services...${NC}"
    docker-compose logs $FOLLOW
else
    echo -e "${YELLOW}Showing logs for $SERVICE...${NC}"
    docker-compose logs $FOLLOW "$SERVICE"
fi
