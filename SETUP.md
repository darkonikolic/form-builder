# Form Builder Project - Setup Guide

## Quick Start

### Prerequisites

- Docker
- Docker Compose
- Make (optional)

### Setup Commands

```bash
# One command setup (recommended)
make setup

# Or step by step:
cp docker/env.example docker/.env
make build
make up
```

## Access Points

- **Web App**: http://localhost:8085
- **pgAdmin**: http://localhost:8081
  - Email: admin@admin.com
  - Password: admin
- **PostgreSQL**: localhost:5433

## Project Structure

```
form-builder/
├── app/                    # Application code
├── docker/                 # Docker configuration
│   ├── docker-compose.yml  # Services orchestration
│   ├── php/               # PHP container
│   ├── nginx/             # Web server
│   ├── node/              # Frontend tools
│   └── env.example        # Environment template
├── task/                   # Project requirements
└── Makefile               # Development commands
```

## Available Commands

```bash
make help           # Show all commands
make setup          # Setup and start everything
make build          # Build containers
make up             # Start services
make down           # Stop services
make logs           # View logs
make clean          # Clean up everything
make ps             # Show running containers
make shell-php      # PHP container shell
make shell-node     # Node.js container shell
make shell-postgres # Database shell
```

## Environment Variables

All configuration is in `docker/.env`:

- Ports (8085, 8081, 5433)
- Database credentials
- Service versions

## Next Steps

1. Access the web app at http://localhost:8085
2. Create database migrations
3. Build your form builder logic
4. Add frontend components

## Troubleshooting

- **Port conflicts**: Check if ports 8085, 8081, 5433 are free
- **Build errors**: Run `make clean` then `make setup`
- **Service issues**: Check logs with `make logs`
