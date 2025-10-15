# API Platform Configuration

## Installation
API Platform is already configured in the project with the following bundles:

```bash
composer require api-platform/core
```

## Bundle Registration
Add to `config/bundles.php`:

```php
ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
```

## Configuration

### Routes
API Platform routes configured in `config/routes/api_platform.yaml`

### Entity Configuration
Entities use annotations/attributes for API configuration:

```php
#[ApiResource(
    shortName: "Config",
    description: "App config",
    operations: [
        new GetCollection(),
        new Get(),
    ],
    security: "is_granted('ROLE_USER')"
)]
```

### Key Annotations
- `#[ApiResource]` - Main entity configuration
- `#[ApiProperty(identifier: true)]` - Primary key identifier
- `operations` - Define available API operations (GET, POST, PUT, DELETE)
- `normalizationContext` - Control serialization groups
- `security` - Define access control

### Available Operations
- `GetCollection` - List entities
- `Get` - Get single entity
- `Post` - Create entity
- `Put/Patch` - Update entity
- `Delete` - Delete entity

### OpenAPI Integration
Automatic documentation available at `/api/docs`

### Security
Access control using Symfony Security component with role-based permissions.

## Serialization Configuration

### Framework Configuration
Add to `config/packages/framework.yaml`:

```yaml
framework:
    secret: '%env(APP_SECRET)%'
    session: true
    serializer:
        enable_attributes: false
        enabled: true
        mapping:
            paths: [ '%kernel.project_dir%/config/serialization/' ]
```

### Serialization Groups
Create serialization configuration in `config/serialization/Cms/Config.yaml`:

```yaml
App\Entity\Cms\Config:
    attributes:
        id:
            groups:
                - 'config:list_read'
                - 'config:item_read'
        keyword:
            groups:
                - 'config:list_read'
                - 'config:item_read'
        value:
            groups:
                - 'config:list_read'
                - 'config:item_read'
```

### Key Points
- `enable_attributes: false` - Use YAML configuration over attributes
- `mapping.paths` - Directory containing serialization configs
- Groups control which fields are exposed in API responses
- `config:list_read` - Fields shown in collection endpoints
- `config:item_read` - Fields shown in single item endpoints
