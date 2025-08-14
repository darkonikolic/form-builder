# Makefile for form-builder project

# Docker Compose commands
.PHONY: help build up down restart logs logs-node clean ps shell-php shell-node shell-postgres shell-composer composer-install check-all setup restart-node node-build

help:
	@echo "Available targets:"
	@echo "  help           - Show this help message"
	@echo "  setup          - Clean up, copy env.example to .env and start services"
	@echo "  build          - Build all Docker containers"
	@echo "  up             - Start all services"
	@echo "  down           - Stop all services"
	@echo "  restart        - Restart all services"
	@echo "  restart-node   - Restart Node.js container for dev mode"
	@echo "  node-build     - Build React assets with Vite"
	@echo "  logs           - Show logs from all services"
	@echo "  logs-node      - Show logs from Node.js container"
	@echo "  clean          - Remove all containers and volumes"
	@echo "  ps             - Show running containers"
	@echo "  shell-php      - Open shell in PHP container"
	@echo "  shell-node     - Open shell in Node.js container"
	@echo "  shell-postgres - Open shell in PostgreSQL container"
	@echo "  shell-composer - Open shell in Composer container"
	@echo "  composer-install - Install PHP dependencies"
	@echo "  check-all      - Fix all files, check pest, run all tests"
	@echo "  js-lint        - Check JavaScript code style with ESLint"
	@echo "  js-lint-fix    - Fix JavaScript code style issues"
	@echo "  js-format      - Format JavaScript code with Prettier"
	@echo "  js-style       - Check JavaScript code style and formatting"
	@echo "  migrate        - Run database migrations"
	@echo "  migrate-fresh  - Drop all tables and re-run migrations"
	@echo "  seed           - Run database seeders"
	@echo "  migrate-fresh-seed - Drop all tables, re-run migrations and seed"
	@echo "  db-recreate    - Drop database, recreate, run migrations and seed"
	@echo "  db-recreate-test - Create test database, run migrations and seed"
	@echo "  rector         - Run Rector for PHP code improvements"
	@echo "  swagger        - Generate Swagger API documentation"

setup:
	@echo "Setting up environment..."
	@echo "Cleaning up previous environment..."
	@make clean
	@echo "Copying environment file..."
	@cp docker/env.example docker/.env
	@echo "Building and starting services..."
	@make build
	@make up
	@echo "Waiting for services to be ready..."
	@sleep 10
	@echo "Setting up database..."
	@make db-recreate
	@echo "Setup complete! Services are running."
	@echo "Access:"
	@echo "  - Web app: http://localhost:8085"
	@echo "  - API docs: http://localhost:8085/api/documentation"
	@echo "  - pgAdmin: http://localhost:8081"
	@echo "  - PostgreSQL: localhost:5433"
	@echo "Test users created:"
	@echo "  - admin@example.com / password"
	@echo "  - test@example.com / password"
	@echo "  - demo@example.com / password"
	@echo ""
	@echo "Running comprehensive code quality check..."
	@make check-all

build:
	docker compose -f docker/docker-compose.yml build

up:
	docker compose -f docker/docker-compose.yml up -d

down:
	docker compose -f docker/docker-compose.yml down

restart:
	docker compose -f docker/docker-compose.yml restart

logs:
	docker compose -f docker/docker-compose.yml logs -f

clean:
	@echo "🧹 Cleaning up Docker environment..."
	docker compose -f docker/docker-compose.yml down -v --remove-orphans
	@echo "✅ Docker Compose services stopped and cleaned"
	
clean-all:
	@echo "🗑️  RADICAL cleanup - removing ALL unused Docker resources..."
	@echo "⚠️  This will delete ALL unused images, volumes, networks, and cache!"
	@echo "Are you sure? (y/N)"
	@read -p "" confirm && if [ "$$confirm" = "y" ] || [ "$$confirm" = "Y" ]; then \
		docker compose -f docker/docker-compose.yml down -v --remove-orphans; \
		docker system prune -af --volumes; \
		echo "✅ All Docker resources cleaned!"; \
	else \
		echo "❌ Cleanup cancelled"; \
	fi

ps:
	docker compose -f docker/docker-compose.yml ps

shell-php:
	docker compose -f docker/docker-compose.yml exec php bash

shell-node:
	docker compose -f docker/docker-compose.yml exec node sh

shell-postgres:
	docker compose -f docker/docker-compose.yml exec postgres psql -U postgres -d form_builder

shell-composer:
	docker compose -f docker/docker-compose.yml exec composer sh

composer-install:
	docker compose -f docker/docker-compose.yml exec composer composer install

check-all:
	@echo "🔧 Running comprehensive code quality check..."
	@echo "1. Fixing all files with Laravel Pint..."
	docker compose -f docker/docker-compose.yml exec composer ./vendor/bin/pint --repair
	@echo "2. Testing code style after fixes..."
	docker compose -f docker/docker-compose.yml exec composer ./vendor/bin/pint --test
	@echo "3. Running Rector for PHP code improvements..."
	docker compose -f docker/docker-compose.yml exec php ./vendor/bin/rector process
	@echo "4. Fixing JavaScript code style issues..."
	docker compose -f docker/docker-compose.yml exec node npm run lint:fix
	@echo "5. Formatting JavaScript code with Prettier..."
	docker compose -f docker/docker-compose.yml exec node npm run format
	@echo "6. Checking JavaScript code style with ESLint..."
	docker compose -f docker/docker-compose.yml exec node npm run lint
	@echo "7. Checking JavaScript formatting with Prettier..."
	docker compose -f docker/docker-compose.yml exec node npm run format:check
	@echo "8. Generating Swagger API documentation..."
	@make swagger
	@echo "9. Recreating test database..."
	@make db-recreate-test
	@echo "10. Running all tests..."
	docker compose -f docker/docker-compose.yml exec php ./vendor/bin/pest
	@echo "✅ All checks completed successfully!"

restart-node:
	docker compose -f docker/docker-compose.yml restart node
	@echo "Node.js container restarted for development mode"

node-build:
	docker compose -f docker/docker-compose.yml exec node npm run build
	@echo "React assets built successfully with Vite!"

logs-node:
	docker compose -f docker/docker-compose.yml logs -f node

js-lint:
	docker compose -f docker/docker-compose.yml exec node npm run lint
	@echo "JavaScript linting completed!"

js-lint-fix:
	docker compose -f docker/docker-compose.yml exec node npm run lint:fix
	@echo "JavaScript linting issues fixed!"

js-format:
	docker compose -f docker/docker-compose.yml exec node npm run format
	@echo "JavaScript code formatted with Prettier!"

js-style:
	docker compose -f docker/docker-compose.yml exec node npm run style
	@echo "JavaScript code style check completed!"

migrate:
	docker compose -f docker/docker-compose.yml exec php php artisan migrate
	@echo "Database migrations completed!"

migrate-fresh:
	docker compose -f docker/docker-compose.yml exec php php artisan migrate:fresh
	@echo "Database refreshed and migrations completed!"

seed:
	docker compose -f docker/docker-compose.yml exec php php artisan db:seed
	@echo "Database seeded successfully!"

migrate-fresh-seed:
	docker compose -f docker/docker-compose.yml exec php php artisan migrate:fresh --seed
	@echo "Database refreshed, migrated and seeded successfully!"

db-recreate:
	@echo "🗑️  Dropping and recreating database..."
	docker compose -f docker/docker-compose.yml exec postgres dropdb -U postgres form_builder --if-exists
	docker compose -f docker/docker-compose.yml exec postgres createdb -U postgres form_builder
	@echo "🔄 Running migrations..."
	docker compose -f docker/docker-compose.yml exec php php artisan migrate
	@echo "🌱 Running seeders..."
	docker compose -f docker/docker-compose.yml exec php php artisan db:seed
	@echo "✅ Database recreated and seeded successfully!"

db-recreate-test:
	@echo "🧪 Creating test database..."
	docker compose -f docker/docker-compose.yml exec postgres dropdb -U postgres form_builder_test --if-exists
	docker compose -f docker/docker-compose.yml exec postgres createdb -U postgres form_builder_test
	@echo "🔄 Running migrations on test database..."
	docker compose -f docker/docker-compose.yml exec php php artisan migrate:fresh --database=testing
	@echo "✅ Test database recreated successfully!"

rector:
	@echo "🔧 Running Rector for PHP code improvements..."
	docker compose -f docker/docker-compose.yml exec composer ./vendor/bin/rector process --dry-run
	@echo "✅ Rector analysis completed!"

swagger:
	@echo "📚 Generating Swagger API documentation..."
	docker compose -f docker/docker-compose.yml exec php php artisan l5-swagger:generate
	@echo "✅ Swagger documentation generated successfully!"
