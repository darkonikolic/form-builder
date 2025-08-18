# Development Setup

## Prerequisites

- Docker and Docker Compose
- Node.js 18+ (for local development)
- PHP 8.2+ (for local development)

## Quick Start

```bash
# Clone and setup
git clone https://github.com/darkonikolic/form-builder.git
cd form-builder
make setup

# Start services
make up

# Access application
open http://localhost:8085
```

## Development Commands

### Code Quality

```bash
make pint          # PHP code style
make rector        # PHP improvements
make lint          # JavaScript linting
make format        # JavaScript formatting
make check-all     # Complete quality pipeline
```

### Testing

```bash
make test          # Run all tests
make test Feature/Forms    # Forms test suite
make test Feature/Fields   # Fields test suite
make test Feature/Auth     # Authentication tests
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

## Access Points

| Service        | URL                   | Credentials             |
| -------------- | --------------------- | ----------------------- |
| **Web App**    | http://localhost:8085 | -                       |
| **pgAdmin**    | http://localhost:8081 | admin@admin.com / admin |
| **PostgreSQL** | localhost:5433        | postgres / postgres     |

## Quality Checks

The `make check-all` command runs the complete quality pipeline:

1. **Laravel Pint** - PHP code style fixing
2. **Rector** - PHP code improvements
3. **ESLint** - JavaScript linting and fixing
4. **Prettier** - JavaScript code formatting
5. **Swagger** - API documentation generation
6. **Database** - Test database recreation
7. **Tests** - Complete test suite execution

## Troubleshooting

- **Port conflicts**: Ensure ports 8085, 8081, 5433 are available
- **Build errors**: Run `make clean` then `make setup`
- **Service issues**: Check logs with `make logs`
- **Test failures**: Verify database is running and migrations are fresh

## Development Workflow

1. **Setup**: `make setup` for initial configuration
2. **Development**: `make up` to start services
3. **Quality**: `make check-all` before commits
4. **Testing**: `make test` to verify functionality
5. **Cleanup**: `make down` when finished
