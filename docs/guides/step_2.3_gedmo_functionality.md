# Gedmo Doctrine Extensions Functionality

## Installation

Install the required bundle:

```bash
composer require stof/doctrine-extensions-bundle
```

**Documentation:** https://github.com/stof/StofDoctrineExtensionsBundle

## Configuration

Create configuration file `config/packages/stof_doctrine_extensions.yaml`:

```yaml
stof_doctrine_extensions:
    default_locale: en
    orm:
        default:
            timestampable: true
            blameable: true
            loggable: true
            softdeleteable: true
            sluggable: false
```

## Usage Examples

### Loggable Functionality

Create log entry class `src/Entity/Log/LogEntry.php`:

```php
<?php

namespace App\Entity\Log;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'log_entry')]
class LogEntry extends AbstractLogEntry
{
}
```

Use in entities:

```php
#[Gedmo\Loggable(logEntryClass: LogEntry::class)]
class EntityClass
{
    #[Gedmo\Versioned]
    private ?string $value = null;
}
```

**Note:** Add `#[Gedmo\Loggable(logEntryClass: LogEntry::class)]` attribute on class and `#[Gedmo\Versioned]` on fields to version.

### SoftDeleteable Functionality

Add to entity:

```php
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class EntityClass
{
    use SoftDeleteAwareTrait;
}
```

**Trait:** `src/Entity/Traits/SoftDeleteAwareTrait.php`

### Timestampable Functionality

**Trait:** `src/Entity/Traits/TimestampsAwareTrait.php`

### Blameable Functionality

**Traits:**
- `src/Entity/Traits/CreatedByAwareTrait.php`
- `src/Entity/Traits/UpdatedByAwareTrait.php`