# Step 1.3: Creating Entities and Database Migrations

## Commands

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:status
php bin/console debug:router | grep config
```

## Config Entity

- Location: `src/Entity/Cms/Config.php`
- Fields: `id`, `keyword` (50 chars), `value` (255 chars)
- Uses `#[AsResource]` for automatic CRUD operations

## AdminMenuBuilder

Decorates default Sylius admin menu using `#[AsDecorator]`:

```php
#[AsDecorator(decorates: 'sylius_admin_ui.knp.menu_builder')]
final readonly class AdminMenuBuilder implements MenuBuilderInterface
```

Creates dashboard + CMS submenu with Config link.

## Migration Process

1. Generate: `doctrine:migrations:diff` - compares schema with entities
2. Execute: `doctrine:migrations:migrate` - applies changes to database
3. Verify: `doctrine:migrations:status` - shows migration status

## Auto-Generated Routes

- `/admin/configs` - index
- `/admin/configs/new` - create  
- `/admin/configs/{id}/edit` - update
- `/admin/configs/{id}` - delete

Access admin at: `https://127.0.0.1:8000/admin`