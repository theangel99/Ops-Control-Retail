#!/bin/bash

echo "================================================"
echo "  OpsControl - Quick Start"
echo "================================================"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running"
    echo "   Please start Docker Desktop and try again"
    exit 1
fi

echo "✓ Docker is running"
echo ""

# Start services
echo "Starting services..."
docker-compose up -d

echo ""
echo "⏳ Waiting for services to initialize..."
echo "   This may take 30-60 seconds on first run..."
echo ""

# Wait for MySQL to be healthy
echo "Waiting for database..."
timeout=60
elapsed=0
while [ $elapsed -lt $timeout ]; do
    if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot_secret > /dev/null 2>&1; then
        echo "✓ Database is ready"
        break
    fi
    sleep 2
    elapsed=$((elapsed + 2))
done

# Wait a bit more for Laravel to initialize
sleep 5

echo ""
echo "================================================"
echo "  OpsControl is ready!"
echo "================================================"
echo ""
echo "  Frontend:  http://localhost:5173"
echo "  Backend:   http://localhost:8000/api"
echo ""
echo "  View logs:    docker-compose logs -f"
echo "  Stop:         docker-compose down"
echo "  Reset data:   docker-compose exec backend php artisan migrate:fresh --seed"
echo ""
echo "================================================"
