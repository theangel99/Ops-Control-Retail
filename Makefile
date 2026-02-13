.PHONY: up down restart logs clean install reset build

# Start all services
up:
	docker-compose up -d
	@echo "OpsControl is starting..."
	@echo "Frontend: http://localhost:5173"
	@echo "Backend API: http://localhost:8000/api"

# Stop all services
down:
	docker-compose down

# Restart all services
restart: down up

# View logs
logs:
	docker-compose logs -f

# Clean everything (including volumes)
clean:
	docker-compose down -v
	rm -rf backend/vendor
	rm -rf frontend/node_modules

# Install dependencies locally
install:
	cd backend && composer install
	cd frontend && npm install

# Reset demo data
reset:
	docker-compose exec backend php artisan migrate:fresh --seed --force

# Build for production
build:
	cd frontend && npm run build

# Help
help:
	@echo "OpsControl - Makefile Commands"
	@echo ""
	@echo "  make up        - Start all services"
	@echo "  make down      - Stop all services"
	@echo "  make restart   - Restart all services"
	@echo "  make logs      - View container logs"
	@echo "  make clean     - Remove all containers and volumes"
	@echo "  make install   - Install dependencies locally"
	@echo "  make reset     - Reset demo database"
	@echo "  make build     - Build frontend for production"
	@echo ""
