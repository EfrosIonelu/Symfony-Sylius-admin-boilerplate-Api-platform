# Step 1.6: Security Configuration

## Installation Commands
```bash
composer require lexik/jwt-authentication-bundle
php bin/console lexik:jwt:generate-keypair
```

## Security Configuration (config/packages/security.yaml)

### Password Hashers
Configure automatic password hashing for Sylius user interfaces:
```yaml
password_hashers:
    Sylius\Component\User\Model\UserInterface: auto
```

### User Providers
Define user provider for Sylius authentication:
```yaml
providers:
    sylius_user_provider:
        id: sylius.user_user_provider.email_or_name_based
```

### Role Hierarchy
Establish role relationships:
```yaml
role_hierarchy:
    ROLE_ADMIN: ROLE_USER
```

### Firewalls Configuration

#### Development Firewall
Disable security for development tools:
```yaml
dev:
    pattern: ^/(_(profiler|wdt)|css|images|js)/
    security: false
```

#### Admin Panel Firewall
Configure form-based authentication for admin interface:
```yaml
admin:
    context: admin
    pattern: "%app.security.admin_regex%"
    provider: sylius_user_provider
    form_login:
        provider: sylius_user_provider
        login_path: sylius_admin_ui_login
        check_path: sylius_admin_ui_login_check
        failure_path: sylius_admin_ui_login
        default_target_path: sylius_admin_ui_dashboard
        use_forward: false
        use_referer: false
    remember_me:
        secret: "%secret%"
        path: "/%app_admin.path_name%"
        name: APP_ADMIN_REMEMBER_ME
        lifetime: 31536000
        remember_me_parameter: _remember_me
    logout:
        path: sylius_admin_ui_logout
        target: sylius_admin_ui_login
        delete_cookies:
            access_token: ~
```

#### API Authentication Firewalls
Configure JWT-based authentication for API endpoints:

**API Login Firewall:**
```yaml
api_login:
    pattern: ^/api/login
    provider: sylius_user_provider
    stateless: true
    json_login:
        check_path: /api/login
        success_handler: lexik_jwt_authentication.handler.authentication_success
        failure_handler: lexik_jwt_authentication.handler.authentication_failure
```

**Important**: The `/api/login` route must be defined in your routing configuration. Add this to your API routes file (e.g., `config/routes.yaml`):
```yaml
api_login_check:
    path: /api/login
```

**API Access Firewall:**
```yaml
api:
    pattern: ^/api
    provider: sylius_user_provider
    stateless: true
    entry_point: jwt
    jwt: true
```

### Access Control Rules
Define path-based access restrictions:
```yaml
access_control:
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/(login|token/refresh), roles: PUBLIC_ACCESS }
    - { path: ^/api/docs, roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: ROLE_USER }
    - { path: "%app.security.admin_regex%/login", roles: PUBLIC_ACCESS }
    - { path: "%app.security.admin_regex%/login-organization", roles: PUBLIC_ACCESS }
    - { path: "%app.security.admin_regex%/login-check", roles: PUBLIC_ACCESS }
    - { path: "%app.security.admin_regex%.*", roles: ROLE_ADMIN }
```

## JWT Authentication Setup

### Bundle Installation
Install JWT authentication bundle:
```bash
composer require lexik/jwt-authentication-bundle
```

### Keypair Generation
Generate RSA keys for JWT signing:
```bash
php bin/console lexik:jwt:generate-keypair
```

### Environment Configuration
Add JWT configuration to `.env`:
```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase
```

## Security Features

### Multi-Context Authentication
- **Admin Context**: Form-based login with session management
- **API Context**: Stateless JWT authentication
- **Remember Me**: Long-term authentication for admin users

### Access Control Strategy
- Public access for login endpoints and API documentation
- Role-based access control (RBAC) with ROLE_USER and ROLE_ADMIN
- Path-based restrictions using regex patterns

### Security Hardening
- Automatic password hashing with secure algorithms
- Stateless API authentication prevents session fixation
- JWT tokens for secure API communication
- Logout cookie deletion for clean session termination

## Notes
- JWT keypair stored in `config/jwt/` directory
- Admin routing pattern configurable via environment variables
- API documentation publicly accessible for development
- Role hierarchy allows admin users to access user-level resources



# Step 1.6.1: RBAC Plugin Configuration

RBAC (Role-Based Access Control) extends basic Symfony security by providing granular permissions management for admin users. It enables fine-grained control over which admin users can access specific admin panel sections and perform particular actions.

## Installation Commands
```bash
composer require "odiseoteam/sylius-rbac-plugin:dev-main"
```

## Configuration

### Repository Configuration (composer.json)
Add custom repository for RBAC plugin:
```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/EfrosIonelu/SyliusRbacPlugin.git"
    }
]
```

### Service Configuration (config/services/factory.yaml)
Add factory and service definitions:
```yaml
sylius_rbac.factory.administration_role:
    class: App\Factory\RbacFactory

sylius.factory.avatar_image:
    class: App\Factory\AvatarImageFactory
    public: true

sylius_rbac.repository.administration_role:
    class: App\Repository\User\AppAdministrationRoleRepository
    factory: [ '@doctrine.orm.entity_manager', 'getRepository' ]
    arguments: [ 'App\Entity\User\AppAdministrationRole' ]

sylius.uploader.image:
    class: App\Services\ImageUploader
    public: true
```

## Database Migration
Create and run migration for RBAC tables:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

## Notes
- Custom repository used due to plugin compatibility issues
- Avatar image factory and image uploader services created for future use
- RBAC admin configuration section will be added to admin panel

# Step 1.6.3: Custom Route RBAC Integration

Extended RBAC plugin to recognize custom admin routes with `app_admin` prefix.

## Commands
```bash
php bin/console cache:clear
php bin/console sylius:fixtures:load default_rbac_fixtures
php bin/console sylius:fixtures:load admin_rbac_fixtures
```

## Created Files
- `src/Access/Checker/AppRouteNameChecker.php` - Decorator extending route name checking
- Service configuration in `config/services.yaml` for decorator registration

## Implementation
- Decorates `Odiseo\SyliusRbacPlugin\Access\Checker\RouteNameCheckerInterface`
- Extends `isAdminRoute()` to include routes with `app_admin` prefix
- Maintains compatibility with existing Sylius and RBAC route patterns

## Configuration
Service decorator configuration added to `config/services.yaml`:
```yaml
App\Access\Checker\AppRouteNameChecker:
    decorates: 'Odiseo\SyliusRbacPlugin\Access\Checker\RouteNameCheckerInterface'
    arguments:
        $decoratedChecker: '@.inner'
```
