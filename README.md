# Form Builder

A modern, multi-language form builder application built with Laravel, React, and Tailwind CSS. This project demonstrates building a full-stack application with proper architecture, testing, and code quality practices.

## What This Project Shows

### Backend Architecture

- Laravel 11 with clean architecture principles
- Repository pattern for data access
- Service layer for business logic
- Comprehensive API with Swagger documentation
- User authentication using Laravel Sanctum

### Frontend Implementation

- React 18 with modern hooks and patterns
- Tailwind CSS for responsive design
- Component-based architecture
- State management with React Context

### Development Practices

- Full test coverage with Pest PHP framework
- Code quality tools (Laravel Pint, Rector, ESLint, Prettier)
- Docker-based development environment
- Automated quality checks and testing

## Core Features

- User registration and authentication
- Multi-language form creation (English, German)
- Dynamic field types (text, email, select, checkbox, etc.)
- Form validation and error handling
- Field ordering and configuration
- RESTful API with proper HTTP status codes

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: React 18, Tailwind CSS
- **Database**: PostgreSQL with UUID primary keys
- **Authentication**: Laravel Sanctum
- **Testing**: Pest PHP framework
- **Code Quality**: Pint, Rector, ESLint, Prettier

## Project Structure

```
form-builder/
├── app/                    # Laravel application root
│   ├── app/               # Laravel app directory
│   │   ├── Http/          # Controllers, Requests, Middleware
│   │   ├── Models/        # Eloquent models
│   │   ├── Services/      # Business logic layer
│   │   ├── Repositories/  # Data access layer
│   │   └── Exceptions/    # Custom exception classes
│   ├── resources/         # Frontend resources
│   │   ├── js/            # React frontend
│   │   │   ├── Components/    # React components
│   │   │   ├── contexts/      # React context providers
│   │   │   ├── Pages/         # Page components
│   │   │   └── components/    # UI components
│   │   ├── css/           # Stylesheets
│   │   └── views/         # Blade templates
│   ├── routes/            # API and web routes
│   ├── tests/             # Test suite
│   ├── database/          # Migrations and seeders
│   └── config/            # Laravel configuration
├── docker/                # Docker configuration
├── config/                # Project configuration
└── task/                  # Project requirements
```

## Key Implementation Details

### Multi-language Support

Forms and fields support multiple locales. Users can create forms in different languages, and the system ensures all required translations are provided for selected locales.

### Field System

The application supports various field types with configurable properties:

- Basic fields: text, email, number, textarea
- Choice fields: select, radio, checkbox
- Special fields: file, date, time, color, range

### Validation System

Comprehensive validation ensures data integrity:

- Form-level validation (names, descriptions, locales)
- Field-level validation (types, configurations, labels)
- User ownership verification
- Cross-reference validation between forms and fields

### Testing Strategy

- Feature tests for API endpoints
- Model tests for data integrity
- Authentication and authorization tests
- Validation and error handling tests

## Getting Started

See [SETUP.md](SETUP.md) for detailed setup instructions.

## API Documentation

This project includes comprehensive **Swagger/OpenAPI documentation** that automatically generates interactive API documentation.

### Access Swagger Documentation

Once the application is running, you can access the API documentation at:

- **Interactive Swagger UI**: http://localhost:8085/api/documentation
- **Raw OpenAPI JSON**: http://localhost:8085/api/documentation.json

The Swagger UI provides:

- Interactive API testing interface
- Request/response examples
- Authentication requirements
- All available endpoints with parameters
- Response schemas and error codes

### API Endpoints

- `POST /api/register` - User registration
- `POST /api/login` - User authentication
- `POST /api/logout` - User logout
- `GET /api/forms` - List user forms
- `POST /api/forms` - Create new form
- `GET /api/forms/{id}` - Get specific form
- `PUT /api/forms/{id}` - Update form
- `DELETE /api/forms/{id}` - Delete form
- `GET /api/forms/{id}/fields` - List form fields
- `POST /api/forms/{id}/fields` - Add field to form

## Access Points

- **Backend API**: http://localhost:8085
- **Swagger Documentation**: http://localhost:8085/api/documentation
- **pgAdmin Database Management**: http://localhost:8081

### Database Access

**pgAdmin Login Credentials:**

- **Email**: admin@admin.com
- **Password**: admin

**Database Connection Details:**

- **Hostname**: postgres
- **Database Name**: form_builder
- **Username**: postgres
- **Password**: postgres
- **Port**: 5432

pgAdmin provides a web-based interface for:

- Database management and administration
- SQL query execution
- Table structure inspection
- Data browsing and editing
- Database backup and restore operations

## Development Workflow

1. **Code Quality**: Run `make check-all` to ensure all quality standards are met
2. **Testing**: Write tests for new features, run `make test` to verify
3. **Documentation**: Update Swagger docs when API changes
4. **Validation**: Ensure all inputs are properly validated

## What This Demonstrates

This project shows:

- Understanding of modern web development practices
- Ability to build full-stack applications
- Knowledge of Laravel and React ecosystems
- Testing and code quality awareness
- API design and documentation skills
- Multi-language application architecture

## Future Enhancements

While this is a demonstration project, it could be extended with:

- Additional field types and validation rules
- Form templates and sharing
- Response collection and analytics
- Advanced user permissions
- Real-time collaboration features

## License

This project is for demonstration purposes.
