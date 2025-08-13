# Makefile for form-builder project

# Docker Compose commands
.PHONY: help build up down restart logs clean ps shell-php shell-node shell-postgres shell-composer composer-install setup

help:
	@echo "Available targets:"
	@echo "  help           - Show this help message"
	@echo "  setup          - Clean up, copy env.example to .env and start services"
	@echo "  build          - Build all Docker containers"
	@echo "  up             - Start all services"
	@echo "  down           - Stop all services"
	@echo "  restart        - Restart all services"
	@echo "  logs           - Show logs from all services"
	@echo "  clean          - Remove all containers and volumes"
	@echo "  ps             - Show running containers"
	@echo "  shell-php      - Open shell in PHP container"
	@echo "  shell-node     - Open shell in Node.js container"
	@echo "  shell-postgres - Open shell in PostgreSQL container"
	@echo "  shell-composer - Open shell in Composer container"
	@echo "  composer-install - Install PHP dependencies"

setup:
	@echo "Setting up environment..."
	@echo "Cleaning up previous environment..."
	@make clean
	@echo "Copying environment file..."
	@cp docker/env.example docker/.env
	@echo "Building and starting services..."
	@make build
	@make up
	@echo "Setup complete! Services are running."
	@echo "Access:"
	@echo "  - Web app: http://localhost:8085"
	@echo "  - pgAdmin: http://localhost:8081"
	@echo "  - PostgreSQL: localhost:5433"

build:
	docker-compose -f docker/docker-compose.yml build

up:
	docker-compose -f docker/docker-compose.yml up -d

down:
	docker-compose -f docker/docker-compose.yml down

restart:
	docker-compose -f docker/docker-compose.yml restart

logs:
	docker-compose -f docker/docker-compose.yml logs -f

clean:
	docker-compose -f docker/docker-compose.yml down -v --remove-orphans
	docker system prune -f

ps:
	docker-compose -f docker/docker-compose.yml ps

shell-php:
	docker-compose -f docker/docker-compose.yml exec php bash

shell-node:
	docker-compose -f docker/docker-compose.yml exec node sh

shell-postgres:
	docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d form_builder

shell-composer:
	docker-compose -f docker/docker-compose.yml exec composer sh

composer-install:
	docker-compose -f docker/docker-compose.yml exec composer composer install
