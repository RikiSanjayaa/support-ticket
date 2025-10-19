#!/bin/bash

# Laravel Support Ticket System - Setup Script
# This script automates the complete setup process for development and production environments
# Usage: ./scripts/setup.sh [dev|prod]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Environment - default to dev
ENV=${1:-dev}
COMPOSE_FILE="compose.dev.yaml"

if [ "$ENV" = "prod" ]; then
    COMPOSE_FILE="compose.prod.yaml"
fi

echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}Laravel Support Ticket System - Setup Script${NC}"
echo -e "${GREEN}Environment: $ENV${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"

# Step 1: Check if Docker Compose file exists
if [ ! -f "$COMPOSE_FILE" ]; then
    echo -e "${RED}✗ Error: $COMPOSE_FILE not found${NC}"
    exit 1
fi

# Step 2: Pull latest images
echo -e "\n${YELLOW}[1/8] Pulling latest Docker images...${NC}"
docker compose -f "$COMPOSE_FILE" pull

# Step 3: Start containers
echo -e "\n${YELLOW}[2/8] Starting containers...${NC}"
docker compose -f "$COMPOSE_FILE" up -d

# Step 4: Wait for services to be ready
echo -e "\n${YELLOW}[3/8] Waiting for services to be ready...${NC}"
sleep 5

# Step 5: Run migrations
echo -e "\n${YELLOW}[4/8] Running database migrations...${NC}"
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan migrate:reset --force 2>/dev/null || true
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan migrate --force

# Step 6: Run seeders
echo -e "\n${YELLOW}[5/8] Running database seeders...${NC}"
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan db:seed --force

# Step 7: Clear caches
echo -e "\n${YELLOW}[6/8] Clearing application caches...${NC}"
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan cache:clear
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan config:clear
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan route:clear
docker compose -f "$COMPOSE_FILE" exec -T php-fpm php artisan view:clear

# Step 8: Build assets (if not using Vite dev server)
echo -e "\n${YELLOW}[7/8] Building assets...${NC}"
docker compose -f "$COMPOSE_FILE" exec -T workspace bash -c "cd /var/www && npm install --legacy-peer-deps" || true
docker compose -f "$COMPOSE_FILE" exec -T workspace bash -c "cd /var/www && npm run build" || echo -e "${YELLOW}Note: Asset building skipped (Vite dev server may be running)${NC}"

# Step 9: Set permissions
echo -e "\n${YELLOW}[8/8] Setting file permissions...${NC}"
docker compose -f "$COMPOSE_FILE" exec -T php-fpm chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Success message
echo -e "\n${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✓ Setup completed successfully!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"

if [ "$ENV" = "dev" ]; then
    echo -e "\n${YELLOW}Development Environment:${NC}"
    echo -e "  • Application URL: http://localhost:8000"
    echo -e "  • Database: PostgreSQL (localhost:5432)"
    echo -e "  • View logs: docker compose -f $COMPOSE_FILE logs -f php-fpm"
    echo -e "  • Run commands: docker compose -f $COMPOSE_FILE exec php-fpm php artisan <command>"
else
    echo -e "\n${YELLOW}Production Environment:${NC}"
    echo -e "  • Ensure all environment variables are set in .env"
    echo -e "  • Database should be properly configured"
    echo -e "  • HTTPS should be configured"
fi

echo -e ""
