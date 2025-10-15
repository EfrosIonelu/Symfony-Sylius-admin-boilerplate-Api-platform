# Step 1: Installing Symfony with Sylius Packages

This document describes the complete process of installing a Symfony project with Sylius packages for the admin panel.

## Prerequisites
- PHP >= 8.2
- Composer installed
- Node.js and npm installed
- Symfony CLI installed (optional, but recommended)

## If you downloaded the project
Setup the .env.local with your credentials.

```bash 
docker compose up -d;
composer install;
php bin/console assets:install
php bin/console lexik:jwt:generate-keypair
npm install;
npm run build;
echo y | php bin/console doctrine:migrations:migrate;
echo y | php bin/console sylius:fixtures:load add_user;
echo y | php bin/console sylius:fixtures:load default_rbac_fixtures;
echo y | php bin/console sylius:fixtures:load admin_rbac_fixtures;

echo y | php bin/console sylius:fixtures:load add_default_language;
echo y | php bin/console sylius:fixtures:load add_default_translations;
```

## Steps

1. [Step 1.0: Basic Symfony Setup](step_1.0_basic_setup.md) - Create Symfony project with basic packages
2. [Step 1.1: Installing and Configuring Sylius Bundles](step_1.1_sylius_bundles.md) - Add Sylius functionality
3. [Step 1.2: Database Configuration](step_1.2_database_config.md) - Set up database connections
4. [Step 1.3: Creating Entities and Database Migrations](step_1.3_entities_migrations.md) - Create entities and manage migrations
5. [Step 1.4: Advanced Entity Configuration](step_1.4_user_customer_bundles.md) - Entity relationships and advanced features
6. [Step 1.5: Grid Configuration](step_1.5_fixtures_factory.md) - Configure data grids for admin interface
7. [Step 1.6: Security Configuration](step_1.6_security_config.md) - Configure authentication and JWT security
8. [Step 1.7: API Platform Configuration](step_1.7_api_platform.md) - Configure API Platform for REST APIs
9. [Step 1.8: Image Processing Configuration](step_1.8_image_processing.md) - Configure image processing and file uploads
10. [Step 1.9: CMS Translation System](step_1.9_translations.md) - Implement multi-language translation system

## Step 2: Advanced Features

11. [Step 2.0: Rich Editor Plugin Integration](step_2.0_monsieurbiz_editor.md) - Add rich text editor functionality with page templates
12. [Step 2.1: Import Export Plugin Integration](step_2.1_import_export.md) - Add data import/export functionality with CSV and Excel support
13. [Step 2.2: Extra grid functionality](step_2.2_extra_grid_functionality.md) - Multiple actions that can be performed on a grid
14. [Step 2.3: Gedmo Doctrine Extensions](step_2.3_gedmo_functionality.md) - Timestampable, blameable, loggable, and softdeleteable functionality
