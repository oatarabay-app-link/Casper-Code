#!/bin/bash

# ============================================
# CasperVPN Deployment Script
# ============================================
# Deploy CasperVPN services to different environments

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Get environment
ENV=${1:-dev}

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Deployment Script${NC}"
echo -e "${GREEN}Environment: ${ENV}${NC}"
echo -e "${GREEN}============================================${NC}"

# Load environment file
if [ -f "config/${ENV}.env" ]; then
    echo -e "${YELLOW}Loading environment from config/${ENV}.env${NC}"
    export $(grep -v '^#' config/${ENV}.env | xargs)
else
    echo -e "${YELLOW}Using default .env file${NC}"
fi

# Pull latest images
if [ "$ENV" != "dev" ]; then
    echo -e "${YELLOW}Pulling latest Docker images...${NC}"
    docker-compose pull
fi

# Stop existing services
echo -e "${YELLOW}Stopping existing services...${NC}"
docker-compose down

# Start services based on environment
if [ "$ENV" = "dev" ]; then
    echo -e "${YELLOW}Starting development environment...${NC}"
    docker-compose -f docker-compose.dev.yml up -d
else
    echo -e "${YELLOW}Starting production environment...${NC}"
    docker-compose up -d
fi

# Wait for services to be healthy
echo -e "${YELLOW}Waiting for services to be healthy...${NC}"
sleep 15

# Check service health
echo -e "${YELLOW}Checking service health...${NC}"
HEALTHY=true

for service in api admin-react admin-php server-agent; do
    if docker-compose ps $service | grep -q "Up"; then
        echo -e "${GREEN}✓ $service is running${NC}"
    else
        echo -e "${RED}✗ $service is not running${NC}"
        HEALTHY=false
    fi
done

if [ "$HEALTHY" = false ]; then
    echo -e "${RED}Some services failed to start. Check logs with: ./scripts/logs.sh${NC}"
    exit 1
fi

# Run health checks
echo -e "${YELLOW}Running health checks...${NC}"
if curl -f http://localhost:8080/health >/dev/null 2>&1; then
    echo -e "${GREEN}✓ API health check passed${NC}"
else
    echo -e "${RED}✗ API health check failed${NC}"
fi

if curl -f http://localhost:8081/health >/dev/null 2>&1; then
    echo -e "${GREEN}✓ Server Agent health check passed${NC}"
else
    echo -e "${RED}✗ Server Agent health check failed${NC}"
fi

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}============================================${NC}"
echo -e ""
echo -e "Services are accessible at:"
echo -e "  - API: http://localhost:8080"
echo -e "  - React Admin: http://localhost:3000"
echo -e "  - PHP Admin: http://localhost:9000"
echo -e "  - Server Agent: http://localhost:8081"
echo -e ""
echo -e "View logs with: ${GREEN}./scripts/logs.sh${NC}"
echo -e "Check status with: ${GREEN}docker-compose ps${NC}"
echo -e ""
