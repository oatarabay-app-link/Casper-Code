#!/bin/bash

# ============================================
# CasperVPN Health Check Script
# ============================================
# Check health status of all CasperVPN services

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}CasperVPN Health Check${NC}"
echo -e "${GREEN}============================================${NC}"
echo -e ""

# Check if services are running
echo -e "${YELLOW}Checking Docker containers...${NC}"
echo -e ""

for service in postgres redis api admin-react admin-php server-agent; do
    if docker-compose ps $service | grep -q "Up"; then
        STATUS=$(docker-compose ps $service | grep "Up" | grep -q "(healthy)" && echo "healthy" || echo "running")
        if [ "$STATUS" = "healthy" ]; then
            echo -e "${GREEN}✓ $service: Healthy${NC}"
        else
            echo -e "${YELLOW}⚠ $service: Running (no health check)${NC}"
        fi
    else
        echo -e "${RED}✗ $service: Not running${NC}"
    fi
done

echo -e ""
echo -e "${YELLOW}Checking HTTP endpoints...${NC}"
echo -e ""

# Check API health
if curl -sf http://localhost:8080/health >/dev/null; then
    echo -e "${GREEN}✓ API (8080): Responding${NC}"
else
    echo -e "${RED}✗ API (8080): Not responding${NC}"
fi

# Check React Admin
if curl -sf http://localhost:3000 >/dev/null; then
    echo -e "${GREEN}✓ React Admin (3000): Responding${NC}"
else
    echo -e "${RED}✗ React Admin (3000): Not responding${NC}"
fi

# Check PHP Admin
if curl -sf http://localhost:9000/health >/dev/null; then
    echo -e "${GREEN}✓ PHP Admin (9000): Responding${NC}"
else
    echo -e "${RED}✗ PHP Admin (9000): Not responding${NC}"
fi

# Check Server Agent
if curl -sf http://localhost:8081/health >/dev/null; then
    echo -e "${GREEN}✓ Server Agent (8081): Responding${NC}"
else
    echo -e "${RED}✗ Server Agent (8081): Not responding${NC}"
fi

echo -e ""
echo -e "${YELLOW}Checking database connections...${NC}"
echo -e ""

# Check PostgreSQL
if docker-compose exec -T postgres pg_isready -U casperuser >/dev/null 2>&1; then
    echo -e "${GREEN}✓ PostgreSQL: Connected${NC}"
else
    echo -e "${RED}✗ PostgreSQL: Connection failed${NC}"
fi

# Check Redis
if docker-compose exec -T redis redis-cli ping >/dev/null 2>&1; then
    echo -e "${GREEN}✓ Redis: Connected${NC}"
else
    echo -e "${RED}✗ Redis: Connection failed${NC}"
fi

echo -e ""
echo -e "${YELLOW}Resource Usage:${NC}"
echo -e ""
docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}" | grep caspervpn

echo -e ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Health Check Complete${NC}"
echo -e "${GREEN}============================================${NC}"
