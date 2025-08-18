# Form Builder Setup Guide

## Prerequisites

- Docker and Docker Compose
- Node.js 18+ (for local development)
- PHP 8.2+ (for local development)

## Quick Setup

### 1. Clone and setup

```bash
git clone https://github.com/dnikolic/form-builder.git
cd form-builder
make setup
```

### 2. Start services

```bash
make up
```

### 3. Run quality checks

```bash
make check-all
```

## Available Commands

### Development Commands

```bash
make pint          # PHP code style
make rector        # PHP improvements
make lint          # JavaScript linting
make format        # JavaScript formatting
make test          # Run all tests
```

### Container Management

```bash
make up            # Start all services
make down          # Stop all services
make logs          # View logs
make clean         # Clean up everything
make ps            # Show running containers
```

### Shell Access

```bash
make shell-php      # PHP container shell
make shell-node     # Node.js container shell
make shell-postgres # Database shell
```

## Testing

### Run all tests

```bash
make test
```

### Run specific test suite

```bash
docker compose -f docker/docker-compose.yml exec php ./vendor/bin/pest --filter="FormApiTest"
docker compose -f docker/docker-compose.yml exec php ./vendor/bin/pest --filter="FieldApiTest"
docker compose -f docker/docker-compose.yml exec php ./vendor/bin/pest --filter="ApiAuthTest"
```

### Run individual tests

```bash
docker compose -f docker/docker-compose.yml exec php ./vendor/bin/pest --filter="user can create form"
```

## Code Quality Checks

The `make check-all` command runs the complete quality check pipeline:

1. **Laravel Pint** - PHP code style fixing
2. **Rector** - PHP code improvements
3. **ESLint** - JavaScript linting and fixing
4. **Prettier** - JavaScript code formatting
5. **Swagger** - API documentation generation
6. **Database** - Test database recreation
7. **Tests** - Complete test suite execution

## Access Points

- **Web App**: http://localhost:8085
- **pgAdmin**: http://localhost:8081
  - Email: admin@admin.com
  - Password: admin
- **PostgreSQL**: localhost:5433

## Troubleshooting

- **Port conflicts**: Check if ports 8085, 8081, 5433 are free
- **Build errors**: Run `make clean` then `make setup`
- **Service issues**: Check logs with `make logs`
- **Test failures**: Ensure database is running and migrations are fresh
