# Step 1.2: Database Configuration and Development Tools

This document describes the installation of additional bundles needed for development and database management.

## Commands

```bash
# Install development tools
composer require symfony/maker-bundle --dev
composer require doctrine/doctrine-migrations-bundle

# Create database
php bin/console doctrine:database:create

# Check migration status
php bin/console doctrine:migrations:status
```

## Additional bundles installed

### Development Tools

#### 1. symfony/maker-bundle ^1.64
- **Purpose**: Code generation tool for rapid development
- **Installation**: `composer require symfony/maker-bundle --dev`
- **Features**:
  - Generate entities, controllers, forms, and more
  - Create CRUD operations automatically
  - Generate authentication systems
  - Create custom commands
- **Usage examples**:
  ```bash
  php bin/console make:entity
  php bin/console make:controller
  php bin/console make:crud
  ```

#### 2. doctrine/doctrine-migrations-bundle ^3.4
- **Purpose**: Database schema versioning and migration management
- **Installation**: `composer require doctrine/doctrine-migrations-bundle`
- **Features**:
  - Automatic migration file generation
  - Database schema versioning
  - Safe database updates
  - Migration rollback capabilities
- **Usage examples**:
  ```bash
  php bin/console doctrine:migrations:diff
  php bin/console doctrine:migrations:migrate
  php bin/console doctrine:migrations:status
  ```

### Core Bundles (already installed)

#### 3. symfony/twig-bundle 7.3.*
- **Purpose**: Template engine integration
- **Features**:
  - Template rendering
  - Template inheritance
  - Built-in filters and functions
  - Integration with Symfony forms
- **Already configured**: Yes (installed with Sylius packages)

## Configuration Files Created

The installation created the following configuration files:

```
config/
├── packages/
│   ├── doctrine_migrations.yaml
│   └── maker.yaml (dev environment only)
migrations/
└── (directory for migration files)
```

## Database Configuration

Before using Doctrine migrations, ensure your database connection is configured in `.env`:

```env
DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=16&charset=utf8"
```

Or for MySQL:
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.32&charset=utf8mb4"
```

## Workflow for Development

With these bundles installed, the typical development workflow becomes:

1. **Create Entity**: `php bin/console make:entity EntityName`
2. **Generate Migration**: `php bin/console doctrine:migrations:diff`
3. **Run Migration**: `php bin/console doctrine:migrations:migrate`
4. **Create CRUD**: `php bin/console make:crud EntityName` (optional)

## Verification

Check that all bundles are properly installed:

```bash
# Check available maker commands
php bin/console list make

# Check migration status
php bin/console doctrine:migrations:status

# Debug available routes
php bin/console debug:router
```

## Next Step

Continue with [Step 1.3: Creating Entities and Database Migrations](step_1.3.md) to create your first entity with Sylius Resource Bundle.
