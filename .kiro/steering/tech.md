# Technology Stack & Development Guidelines

## Tech Stack

**Backend**
- **Laravel 12.0**: PHP framework for API and business logic
- **PHP 8.2+**: Required minimum version
- **MySQL**: Primary database for all application data
- **Laravel Sanctum**: API authentication and session management

**Frontend**
- **Inertia.js**: SPA experience without separate API endpoints
- **Vue.js 3.3**: Component-based frontend framework with Composition API
- **Tailwind CSS 4.0**: Utility-first CSS framework for styling
- **Heroicons**: Icon library for consistent UI elements
- **Vite**: Build tool and development server

**Development Tools**
- **Laravel Pint**: Code formatting and style enforcement
- **PHPUnit**: Backend testing framework
- **Faker**: Test data generation
- **Laravel Sail**: Docker development environment

## Architecture Patterns

**Backend Patterns**
- **Repository Pattern**: Data access abstraction (e.g., `ClientRepository`)
- **Service Layer**: Business logic encapsulation (e.g., `ClientInformationService`)
- **Request Validation**: Form request classes for input validation
- **Custom Rules**: Reusable validation rules (e.g., `SSNFormat`, `PhoneNumberFormat`)
- **Model Events**: Automatic audit logging via Eloquent events
- **Soft Deletes**: Data preservation for compliance requirements

**Frontend Patterns**
- **Composition API**: Vue 3 reactive composition patterns
- **Component Reusability**: Shared components like `DynamicTable`
- **Form Validation**: Real-time client-side validation with server-side backup
- **Conditional Rendering**: Dynamic form sections based on user input
- **State Management**: Reactive data handling without external state libraries

## Common Commands

**Development**
```bash
# Start development server
npm run dev

# Build for production
npm run build

# Run PHP tests
php artisan test

# Code formatting
./vendor/bin/pint

# Database operations
php artisan migrate
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**Laravel Artisan Commands**
```bash
# Generate models, controllers, requests
php artisan make:model ModelName -mcr
php artisan make:request RequestName
php artisan make:rule RuleName
php artisan make:service ServiceName

# Database
php artisan migrate:fresh --seed
php artisan tinker
```

## Code Standards

**PHP/Laravel**
- Follow PSR-12 coding standards
- Use type hints for all method parameters and return types
- Implement comprehensive validation in Request classes
- Use Eloquent relationships instead of manual joins
- Always use mass assignment protection (`$fillable` arrays)
- Implement audit logging for sensitive data changes

**Vue.js/JavaScript**
- Use Composition API over Options API
- Implement proper prop validation and TypeScript-style documentation
- Use reactive refs and computed properties appropriately
- Follow component naming conventions (PascalCase for components)
- Implement proper error handling and user feedback

**CSS/Styling**
- Use Tailwind utility classes over custom CSS
- Maintain consistent spacing and color schemes
- Implement responsive design patterns
- Use semantic color names (primary, secondary, etc.)

## Security Guidelines

- Never expose SSN or sensitive data in JSON responses
- Use Laravel's built-in CSRF protection
- Implement proper input validation and sanitization
- Use parameterized queries (Eloquent handles this)
- Encrypt sensitive data at rest
- Implement proper session management and timeouts