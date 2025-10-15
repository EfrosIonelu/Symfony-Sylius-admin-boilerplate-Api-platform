# Step 2.0: Rich Editor Plugin Integration

Integration guide for `monsieurbiz/sylius-rich-editor-plugin` with page functionality.

## Installation Commands

```bash
# Install rich editor plugin
composer require monsieurbiz/sylius-rich-editor-plugin

# Install required dependency (not mentioned in plugin docs but necessary)
composer require symfony/ux-live-component

# Install assets and build
php bin/console assets:install
npm install
npm run build
```

## Overview

The `monsieurbiz/sylius-rich-editor-plugin` provides a rich text editor with customizable UI elements for Symfony applications. It enables content creation with various blocks (text, images, buttons, quotes, etc.) and integrates seamlessly with Sylius admin interface for managing rich content in entities.


## FormBuilder Integration

Add rich editor field to your forms:

```php
use MonsieurBiz\SyliusRichEditorPlugin\Form\Type\RichEditorType;

// In your form builder
->add('content', RichEditorType::class, [
    'label' => 'app.ui.content',
    'required' => false,
    'attr' => [
        'placeholder' => 'app.ui.add_page_content',
    ],
]);
```

## Template Rendering

Display rich editor content in templates:

```twig
{{ page.content|monsieurbiz_richeditor_render_field }}
```

## Configuration for Non-Sylius Bundles

If Sylius bundle is not activated, add the following configurations:

### 1. Twig Configuration

```yaml
# config/packages/twig.yaml
twig:
    globals:
        sylius_base_locale: "%locale%"
        sylius:
            localeCode: 'en'
```

### 2. Locale Context Service

Create locale context implementation:

```php
<?php

use Sylius\Component\Locale\Context\LocaleContextInterface;

class LocaleContext implements LocaleContextInterface
{
    // Implementation details
}
```

Register the service:

```yaml
# config/services.yaml
services:
    app.context.locale:
        class: App\Context\LocaleContext
        arguments:
            $requestStack: '@request_stack'
            $localeProvider: '@app.provider.locale'
        tags:
            - { name: sylius.context.locale}
    
    # Alias for autowiring
    Sylius\Component\Locale\Context\LocaleContextInterface: '@app.context.locale'
```

### 3. Parameters Configuration

```yaml
# config/packages/parameters.yaml
parameters:
    sylius_core.public_dir: '%kernel.project_dir%/public'
```
