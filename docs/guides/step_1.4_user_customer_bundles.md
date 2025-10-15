# Step 1.4: Installing Sylius Customer and User Bundles

## Commands

```bash
composer require sylius/customer-bundle --with-all-dependencies
composer require sylius/user-bundle --with-all-dependencies
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Bundle Registration

Add to `config/bundles.php`:
```php
Sylius\Bundle\UserBundle\SyliusUserBundle::class => ['all' => true],
Sylius\Bundle\CustomerBundle\SyliusCustomerBundle::class => ['all' => true],
```

## Entity Configuration

### User Entities Structure
```
src/Entity/User/
├── AppUser.php
├── AppCustomer.php
├── AppUserOAuth.php
└── Interfaces/
    ├── AppUserInterface.php
    ├── AppCustomerInterface.php
    └── UserOAuthInterface.php
```

### Doctrine Configuration

Add to `config/packages/doctrine.yaml`:
```yaml
resolve_target_entities:
    Sylius\Component\User\Model\UserInterface: App\Entity\User\AppUser
    Sylius\Component\Customer\Model\CustomerInterface: App\Entity\User\AppCustomer
    Sylius\Component\User\Model\UserOAuthInterface: App\Entity\User\AppUserOAuth
```

### Sylius Configuration

Create `config/packages/sylius_user.yaml`:
```yaml
sylius_user:
    resources:
        user:
            user:
                classes:
                    model: App\Entity\User\AppUser
        admin:
            user:
                classes:
                    model: App\Entity\User\AppUser

sylius_customer:
    resources:
        customer:
            classes:
                model: App\Entity\User\AppCustomer
```

## Entity Details

- **AppUser**: Admin user entity extending Sylius BaseUser
- **AppCustomer**: Customer entity extending Sylius BaseCustomer  
- **AppUserOAuth**: OAuth authentication support
- **Interfaces**: Type definitions for dependency injection

## Database Migration

Migration creates:
- `app_user` table - Admin users
- `app_customer` table - Customers
- `app_user_oauth` table - OAuth tokens
- Required foreign key relationships