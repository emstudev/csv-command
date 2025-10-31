#!/bin/bash

# Docker Compose Build and Composer Install Script
# This script builds Docker containers and runs composer install

set -e

echo "🐳 Docker env first time setup"
echo "=================================================="

#echo "Building Docker containers..."
#docker compose build --no-cache

echo "Starting Docker containers..."
docker compose up -d

echo "Waiting for containers to be ready..."
sleep 3

echo "Running composer install..."
docker exec -ti symfony_php composer install

echo "Setting up database..."
docker exec -ti symfony_php php bin/console make:migration
docker exec -ti symfony_php php bin/console doctrine:migrations:migrate --no-interaction

echo "=================================================="
echo "All done! Your application is ready."