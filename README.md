# Form Builder

A production-ready, multi-language form builder application built with modern web technologies. Features a robust Laravel backend with clean architecture and a responsive React frontend with Tailwind CSS.

## Features

- **User Management**: Secure authentication and authorization with Laravel Sanctum
- **Form Builder**: Dynamic form creation with support for multiple languages
- **Field System**: Comprehensive field types (text, email, select, checkbox, file, date, etc.)
- **Validation**: Multi-level validation with custom rules and error handling
- **API-First**: RESTful API with comprehensive Swagger documentation
- **Responsive UI**: Modern React frontend with mobile-first design
- **Multi-language**: Built-in internationalization support (English, German)

## Architecture

### Backend

- **Laravel 11** with clean architecture principles
- **Repository pattern** for data access abstraction
- **Service layer** for business logic separation
- **Eloquent ORM** with PostgreSQL database
- **API authentication** using Laravel Sanctum
- **Comprehensive testing** with Pest PHP framework

### Frontend

- **React 18** with modern hooks and patterns
- **Component-based architecture** with proper separation of concerns
- **Tailwind CSS** for responsive design system
- **Custom hooks** for state management
- **Error boundaries** for graceful error handling
- **Accessibility-first** approach with ARIA labels and keyboard navigation

## Quick Start

```bash
# Clone repository
git clone https://github.com/darkonikolic/form-builder.git
cd form-builder

# Setup and start
make setup
make up

# Access application
open http://localhost:8085
```

See [SETUP.md](SETUP.md) for detailed development instructions.

## Tech Stack

| Category           | Technology                             |
| ------------------ | -------------------------------------- |
| **Backend**        | Laravel 11, PHP 8.2+, PostgreSQL       |
| **Frontend**       | React 18, Tailwind CSS, Vite           |
| **Authentication** | Laravel Sanctum                        |
| **Testing**        | Pest PHP, PHPUnit                      |
| **Code Quality**   | Laravel Pint, Rector, ESLint, Prettier |
| **Development**    | Docker, Docker Compose                 |

## Project Structure

```
form-builder/
├── app/                    # Laravel application
│   ├── Http/              # Controllers, Requests, Middleware
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic layer
│   ├── Repositories/      # Data access layer
│   └── Exceptions/        # Custom exception classes
├── resources/js/          # React frontend
│   ├── Components/        # React components
│   ├── contexts/          # React context providers
│   ├── hooks/             # Custom React hooks
│   └── components/ui/     # Reusable UI components
├── routes/                # API and web routes
├── tests/                 # Test suite
└── docker/                # Development environment
```

## API Endpoints

| Method   | Endpoint                 | Description         |
| -------- | ------------------------ | ------------------- |
| `POST`   | `/api/register`          | User registration   |
| `POST`   | `/api/login`             | User authentication |
| `POST`   | `/api/logout`            | User logout         |
| `GET`    | `/api/forms`             | List user forms     |
| `POST`   | `/api/forms`             | Create new form     |
| `GET`    | `/api/forms/{id}`        | Get specific form   |
| `PUT`    | `/api/forms/{id}`        | Update form         |
| `DELETE` | `/api/forms/{id}`        | Delete form         |
| `GET`    | `/api/forms/{id}/fields` | List form fields    |
| `POST`   | `/api/forms/{id}/fields` | Add field to form   |

## Documentation

- **Interactive API Docs**: http://localhost:8085/api/documentation
- **Raw OpenAPI**: http://localhost:8085/api/documentation.json
- **Setup Guide**: [SETUP.md](SETUP.md)

## Testing

```bash
# Run all tests
make test

# Run specific test suites
make test Feature/Forms
make test Feature/Fields
make test Feature/Auth
```

## Development

### Code Quality

```bash
make pint          # PHP code style
make rector        # PHP improvements
make lint          # JavaScript linting
make format        # JavaScript formatting
make check-all     # Complete quality pipeline
```

### Container Management

```bash
make up            # Start services
make down          # Stop services
make logs          # View logs
make shell-php     # PHP container access
```

## Key Implementation Details

### Multi-language Support

Forms and fields support multiple locales with automatic validation ensuring all required translations are provided.

### Field System

Extensible field system supporting various types with configurable properties, validation rules, and conditional logic.

### Validation System

Multi-level validation system ensuring data integrity across forms, fields, and user interactions.

### Security

- CSRF protection
- SQL injection prevention
- XSS protection
- User ownership verification
- Input sanitization

## Production Readiness

This application is designed for production use with:

- **Scalable architecture** following Laravel best practices
- **Comprehensive error handling** with custom exceptions
- **Performance optimization** with proper database indexing
- **Security best practices** following OWASP guidelines
- **Monitoring and logging** capabilities
- **Docker deployment** ready

## Future Enhancements

- Advanced form templates and sharing
- Real-time collaboration features
- Response collection and analytics
- Advanced user permissions and roles
- API rate limiting and caching
- Webhook integrations
- Advanced field types and validation rules

## License

This project is licensed under the MIT License.
