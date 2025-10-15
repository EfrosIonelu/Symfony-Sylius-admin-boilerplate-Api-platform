# Step 1.9: CMS Translation System Implementation

## Commands

```bash
# Load translation fixtures
php bin/console sylius:fixtures:load add_default_translations --no-interaction
```

## Overview

Implements a standardized CMS translation system using Sylius TranslatableTrait and AbstractTranslation for multi-language content management.

## Architecture

### Core Entities

**Translation Entity** (`src/Entity/Cms/Translation.php`):
- Uses `TranslatableTrait` from Sylius
- Implements `ResourceInterface` and `TranslatableInterface`
- Contains translation key (`key` field with backticks for MySQL reserved word)
- Methods: `getValue()`, `setValue()`, `createTranslation()`

**TranslationTranslation Entity** (`src/Entity/Cms/TranslationTranslation.php`):
- Extends `AbstractTranslation` from Sylius
- Implements `ResourceInterface` and `TranslationInterface`
- Contains translation value and locale (inherited)
- Auto-handles locale management via AbstractTranslation

### Key Features

- **Standard Sylius Pattern**: Follows TranslatableTrait/AbstractTranslation pattern
- **API Integration**: Full API Platform support with pagination
- **Admin Grid**: Complete CRUD interface in admin panel
- **Factory & Fixtures**: Standardized data generation and seeding

## Configuration

### Resource Configuration
Add to `sylius_resource.yaml`:
```yaml
sylius_resource:
    resources:
        app.translation:
            classes:
                model: App\Entity\Cms\Translation
            translation:
                classes:
                    model: App\Entity\Cms\TranslationTranslation
```

### Fixture Configuration
**Translation Suite** (`config/services/fixtures/translation.fixture.yaml`):
```yaml
sylius_fixtures:
    suites:
        add_default_translations:
            fixtures:
                app_translation:
                    options:
                        custom:
                            - key: "app.welcome.message"
                              translations:
                                  en: "Welcome to the app"
                                  ro: "Salut pe aplicația noastră"
```

## Implementation Details

### Factory Pattern
TranslationFactory creates Translation with nested TranslationTranslation entities for each locale:
```php
foreach ($options['translations'] as $locale => $value) {
    $translationTranslation = new TranslationTranslation();
    $translationTranslation->setLocale($locale);
    $translationTranslation->setValue($value);
    $translation->addTranslation($translationTranslation);
}
```

### Database Schema
- `app_cms_translation`: Main translation keys
- `app_cms_translation_translation`: Localized values with locale

### Usage Examples

```php
// Get translation value
$translation->getValue('en'); // Returns English translation
$translation->getValue(); // Returns current locale translation

// Set translation value  
$translation->setValue('Hello World', 'en');
```
