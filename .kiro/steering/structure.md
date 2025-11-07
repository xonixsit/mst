# Project Structure & Organization

## Directory Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/           # Admin dashboard controllers
│   │   └── Requests/            # Form request validation classes
│   ├── Models/                  # Eloquent models with relationships
│   ├── Repositories/            # Data access layer abstraction
│   ├── Rules/                   # Custom validation rules
│   └── Services/                # Business logic services
├── database/
│   ├── factories/               # Model factories for testing
│   ├── migrations/              # Database schema migrations
│   └── seeders/                 # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/          # Reusable Vue components
│   │   └── Pages/               # Inertia.js page components
│   └── css/                     # Tailwind CSS files
├── routes/
│   ├── web.php                  # Web routes (Inertia)
│   └── api.php                  # API routes (if needed)
└── public/                      # Static assets and entry point
```

## Backend Organization

**Models** (`app/Models/`)
- Follow singular naming convention (e.g., `Client`, `ClientSpouse`)
- Include relationships, scopes, and business logic methods
- Implement audit logging through model events
- Use `$fillable` arrays for mass assignment protection
- Hide sensitive fields like SSN in `$hidden` array

**Controllers** (`app/Http/Controllers/`)
- Organize by feature area (e.g., `Admin/ClientController`)
- Keep controllers thin - delegate to services
- Return Inertia responses for page rendering
- Handle both web and AJAX requests appropriately

**Services** (`app/Services/`)
- Encapsulate complex business logic
- Handle data validation and transformation
- Coordinate between multiple models/repositories
- Examples: `ClientInformationService`, `ClientValidationService`

**Repositories** (`app/Repositories/`)
- Abstract database queries from controllers
- Provide reusable query methods
- Handle complex filtering and searching
- Example: `ClientRepository` with advanced filtering

**Validation** (`app/Http/Requests/` & `app/Rules/`)
- Form requests for complex validation scenarios
- Custom rules for business-specific validation (SSN, phone formats)
- Centralized validation messages and error handling

## Frontend Organization

**Pages** (`resources/js/Pages/`)
- Organized by feature area matching backend structure
- Admin pages under `Admin/` subdirectory
- Each page corresponds to a controller action
- Examples: `Admin/Clients/Index.vue`, `Admin/Clients/Show.vue`

**Components** (`resources/js/Components/`)
- Reusable UI components across the application
- Follow PascalCase naming convention
- Key components:
  - `DynamicTable.vue`: Flexible table with add/remove rows
  - `ClientInformationManager.vue`: Multi-section client forms
  - `BulkOperationsModal.vue`: Batch operations interface

**Component Patterns**
- Multi-section forms with logical groupings:
  - Personal Details
  - Spouse Details (conditional)
  - Employee Information
  - Project Details
  - Assets Management
  - Expenses Management
- Dynamic tables for repeatable data (assets, expenses)
- Conditional field display based on form state
- Real-time validation with error feedback

## Database Schema Patterns

**Naming Conventions**
- Table names: plural snake_case (e.g., `clients`, `client_spouses`)
- Foreign keys: `{model}_id` (e.g., `client_id`, `user_id`)
- Timestamps: Always include `created_at`, `updated_at`
- Soft deletes: Include `deleted_at` for important entities

**Relationship Patterns**
- One-to-One: Client → ClientSpouse
- One-to-Many: Client → ClientEmployees, ClientAssets, ClientExpenses
- Many-to-One: Client → User (assigned tax professional)
- Polymorphic: AuditLog → various models

**Migration Organization**
- Chronological naming with descriptive suffixes
- Performance indexes in separate migration
- Foreign key constraints for data integrity

## File Naming Conventions

**PHP Files**
- Models: PascalCase singular (e.g., `Client.php`)
- Controllers: PascalCase with Controller suffix (e.g., `ClientController.php`)
- Services: PascalCase with Service suffix (e.g., `ClientInformationService.php`)
- Requests: PascalCase with Request suffix (e.g., `StoreClientRequest.php`)

**Vue Files**
- Components: PascalCase (e.g., `DynamicTable.vue`)
- Pages: Match controller structure (e.g., `Admin/Clients/Index.vue`)

**Database Files**
- Migrations: `YYYY_MM_DD_HHMMSS_descriptive_name.php`
- Seeders: PascalCase with Seeder suffix (e.g., `ClientInformationSeeder.php`)

## Route Organization

**Web Routes** (`routes/web.php`)
- Group related routes with prefixes and middleware
- Admin routes under `/admin` prefix
- Client routes under `/client` prefix
- Use resource routes where appropriate
- Named routes for easy URL generation

**Route Patterns**
```php
// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('clients', ClientController::class);
    Route::post('clients/bulk', [ClientController::class, 'bulkOperation']);
});

// Client routes
Route::prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [ClientDashboardController::class, 'index']);
});
```

## Configuration Management

**Environment Variables**
- Database credentials in `.env`
- Application keys and secrets
- Feature flags for development/production differences
- Third-party service configurations

**Config Files**
- Database configuration in `config/database.php`
- Application settings in `config/app.php`
- Custom configurations for business rules