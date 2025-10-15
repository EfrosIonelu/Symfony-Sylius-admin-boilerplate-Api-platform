# Step 1.5: User Fixtures and Factory

## Commands

```bash
# List available fixtures
php bin/console sylius:fixtures:list

# Load specific fixture suite
php bin/console sylius:fixtures:load add_default_clinical_trial_periods
```

## Factory Setup

### AppUserFactory Structure
```
src/Factory/AppUserFactory.php - Creates User entities with fake data
config/services/factory.yaml - Factory service configuration  
```

### Factory Configuration

Add `config/services/factory.yaml`:
```yaml
services:
    App\Factory\AppUserFactory:
        arguments:
            $appUserFactory: '@sylius.factory.admin_user'
            $appCustomerFactory: '@sylius.factory.customer'
```

## Fixture Setup

### AppUserFixture Structure  
```
src/Fixture/AppUserFixture.php - User fixture class
config/services/fixtures.yaml - Fixture service configuration
```

### Fixture Configuration

Add `config/services/fixtures.yaml`:
```yaml
services:
    App\Fixture\AppUserFixture:
        arguments:
            $exampleFactory: '@App\Factory\AppUserFactory'
            $objectManager: '@doctrine.orm.entity_manager'
```

## Service Configuration

Update `config/services.yaml`:
```yaml
imports:
    - { resource: services/factory.yaml }
    - { resource: services/fixtures.yaml }

services:
    App\:
        resource: '../src/'
        exclude:
            - '../src/Factory/'
            - '../src/Fixture/'
```

## Finding Service IDs

Debug available Sylius factories:
```bash
php bin/console debug:container | grep factory
php bin/console debug:container | grep user
```

Common service IDs:
- `@sylius.factory.admin_user` - Admin user factory
- `@sylius.factory.customer` - Customer factory
- `@doctrine.orm.entity_manager` - Entity manager

## Factory Features

- **Faker Integration**: Generates random user data
- **Configurable Options**: Username, email, password, roles
- **Customer Association**: Automatically creates linked customer
- **Role Support**: ROLE_USER, ROLE_ADMIN, custom roles

## Fixture Suite Configuration

Create `config/packages/sylius_fixtures.yaml`:
```yaml
sylius_fixtures:
    suites:
        add_user:
            listeners:
                orm_purger: false
                logger: ~
            fixtures:
                user_user:
                    name: "user_user"
                    options:
                        custom:
                            -
                                email: "admin@example.com"
                                password: "password123"
                                enabled: true
                                first_name: "Admin"
                                last_name: "User"
                                role: 'ROLE_ADMIN'
                            -   email: "user@example.com"
                                password: "password123"
                                enabled: true
                                first_name: "Test"
                                last_name: "User"
                                role: 'ROLE_USER'
```

## Usage Examples

Factory creates users with:
- Random username and email (via Faker)
- Default password: `password123`
- Enabled by default
- Associated customer with first/last name

Fixture suite creates:
- Admin user: `admin@example.com` / `password123`
- Regular user: `user@example.com` / `password123`

## Documentation References

- [Sylius Fixtures Bundle](https://github.com/Sylius/SyliusFixturesBundle/blob/1.8/docs/installation.md)
- [Old Sylius Fixtures Docs](https://old-docs.sylius.com/en/1.14/customization/fixtures.html)
