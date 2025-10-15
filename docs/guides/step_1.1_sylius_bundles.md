# Step 1.1: Installing and Configuring Sylius Bundles

This document covers the installation and configuration of Sylius packages for admin panel functionality.

## Commands

```bash
# Install core Sylius packages
composer require sylius/resource-bundle sylius/grid-bundle --with-all-dependencies
composer require sylius/bootstrap-admin-ui sylius/ui-translations --with-all-dependencies
composer require pagerfanta/doctrine-orm-adapter symfony/ux-autocomplete

# Complete frontend setup
npm install --force
npm run build

# Install assets
php bin/console assets:install

# Clear cache
php bin/console cache:clear
```

## 1. Installing Sylius Core Packages

First install the core bundles:
```bash
composer require sylius/resource-bundle sylius/grid-bundle --with-all-dependencies
```

Then install the admin interface:
```bash
composer require sylius/bootstrap-admin-ui sylius/ui-translations --with-all-dependencies
```

Additional required packages:
```bash
composer require pagerfanta/doctrine-orm-adapter symfony/ux-autocomplete
```

## 2. Bundle Registration Fix

**CRITICAL**: After installing Sylius packages, manually add missing bundles to `config/bundles.php`:

Add after the SecurityBundle line:
```php
Sylius\TwigHooks\SyliusTwigHooksBundle::class => ['all' => true],
Sylius\TwigExtra\Symfony\SyliusTwigExtraBundle::class => ['all' => true],
Sylius\Bundle\ResourceBundle\SyliusResourceBundle::class => ['all' => true],
Sylius\AdminUi\Symfony\SyliusAdminUiBundle::class => ['all' => true],
Sylius\BootstrapAdminUi\Symfony\SyliusBootstrapAdminUiBundle::class => ['all' => true],
Sylius\UiTranslations\Symfony\SyliusUiTranslationsBundle::class => ['all' => true],
```

**Why this is needed**: Some Sylius bundle recipes are ignored during installation, requiring manual registration.

## 3. Manual Configuration Files

### Services Configuration

Add to `config/services.yaml`:
```yaml
parameters:
    locale: 'en'
```

### Package Configuration

Create `config/packages/sylius_bootstrap_admin_ui.yaml`:
```yaml
imports:
    - { resource: '@SyliusBootstrapAdminUiBundle/config/app.php' }
```

Create `config/packages/sylius_resource.yaml`:
```yaml
# @see https://github.com/Sylius/SyliusResourceBundle/blob/master/docs/index.md
sylius_resource:
    # Override default settings
    #settings:

    # Configure the mapping for your resources
    mapping:
        paths:
            - '%kernel.project_dir%/src/Entity'

    # Configure your resources
    resources:
    #app.book:
    #classes:
    #model: App\Entity\Book

```

### Routes Configuration

Create `config/routes/sylius_admin_ui.yaml`:
```yaml
sylius_admin_ui:
    resource: '@SyliusAdminUiBundle/Resources/config/routing.yml'
    prefix: /admin
```

Create `config/routes/sylius_resource.yaml`:
```yaml
sylius_resource:
    resource: '@SyliusResourceBundle/Resources/config/routing.yml'
```

## 4. Frontend Configuration

Complete frontend setup:
```bash
npm install --force
npm run build
```

Install Symfony assets:
```bash
php bin/console assets:install
```

## 5. Clear Cache

After configuration:
```bash
php bin/console cache:clear
```

## Installed Bundles Details

### Core Bundles

#### sylius/resource-bundle ^1.13
- **Purpose**: Core functionality for managing entities as resources
- **Features**: CRUD operations, validation, serialization, Doctrine ORM integration
- **Documentation**: https://stack.sylius.com/resource/index/installation

#### sylius/grid-bundle ^1.13
- **Purpose**: Advanced grid system for displaying data
- **Features**: Sorting, filtering, pagination, bulk actions, data export
- **Documentation**: https://stack.sylius.com/grid/index/installation

### Admin Interface Bundles

#### sylius/bootstrap-admin-ui ^0.8.1
- **Purpose**: Modern Bootstrap-based admin interface
- **Features**: Responsive layout, pre-built UI components, customizable theming
- **Dependencies**: sylius/admin-ui, sylius/twig-extra, sylius/twig-hooks, knplabs/knp-menu-bundle

#### sylius/ui-translations ^0.8.1
- **Purpose**: User interface translations
- **Features**: Translated UI messages, multi-language support, Symfony Translation integration

## Important Notes

1. Always use `--with-all-dependencies` for Sylius packages to avoid version conflicts
2. Check `config/bundles.php` after installation and add missing bundles manually
3. The `behat/transliterator` package is marked as abandoned but doesn't affect functionality
4. Webpack.config.js already has Stimulus Bridge configuration enabled
5. Compiled assets are located in `public/build/`

## Next Step

Continue with [Step 1.2: Database Configuration](step_1.2.md) to set up database connections and development tools.
