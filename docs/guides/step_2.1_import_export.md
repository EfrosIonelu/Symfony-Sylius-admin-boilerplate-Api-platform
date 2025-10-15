# Step 2.1: Import Export Plugin Integration

Integration guide for `friendsofsylius/sylius-import-export-plugin` for data import/export functionality.

## Overview

The `friendsofsylius/sylius-import-export-plugin` provides import and export functionality for Sylius entities. It enables bulk data operations with CSV and Excel formats, supporting async processing via message queues for large datasets.

## Installation Commands

```bash
composer require friendsofsylius/sylius-import-export-plugin
composer require symfony/amqp-messenger
```

## Grid Configuration

Add export/import action templates:

```yaml
# config/packages/sylius_grid.yaml
sylius_grid:
    templates:
        action:
            export: "admin/grid/action/export.html.twig"
            import: "admin/grid/action/import.html.twig"
```

Add actions to grid:

```php
->addActionGroup(
    MainActionGroup::create(
        CreateAction::create([
            'position' => 3,
        ]),
        ExportAction::create(
            ['exports' => [
                'csv' => [
                    'route' => 'app_export_data_config_index',
                    'parameters' => [
                        'criteria' => $criteria,
                        'format' => 'csv',
                    ],
                ],
                'excel' => [
                    'route' => 'app_export_data_config_index',
                    'parameters' => [
                        'criteria' => $criteria,
                        'format' => 'xlsx',
                    ],
                ],
            ]]
        ),
        ImportAction::create([
            'icon' => 'material-symbols:upload',
            'route' => 'app_backend_config_import',
            'parameters' => [
                'resource' => 'app.config',
            ],
        ])
    )
)
```

## Export Config entity example

Key files for export functionality:

### Route Configuration
```yaml
# config/routes/admin/export/config.yaml
# Add export route to identify entity
```

### Service Configuration  
```yaml
# config/services/exporter/config_exporter.yaml
# Service settings for export functionality
```

### Resource Plugin
```php
# src/Exporter/Plugin/ConfigResourcePlugin.php
# Plugin that maps required columns for export with entity data
```

## Import Config entity example

Key files for import functionality:

### Message Queue Configuration
```yaml
# config/packages/messenger.yaml
# Add message to queue (requires symfony/amqp-messenger)
```

### Route Configuration
```yaml
# config/routes/admin/import/config.yaml
# Route to import form
```

### Service Configuration
```yaml
# config/services/importer/config_importer.yaml
# Configure import services
```

### Message Components
```php
# src/Message/Config/ConfigImportMessage.php
# Message definition

# src/MessageHandler/Config/ConfigImportMessageHandler.php  
# Service that processes the message

# src/Importer/Config/ConfigImporter.php
# Import action definition

# src/Importer/Config/ConfigProcessor.php
# Processing service definition
```

## Async Export Functionality

Async export capability has been added to handle large datasets (100k+ records) that exceed standard 30-second timeout limits. Export processing can take 3+ minutes, requiring background processing with database persistence.

### Implementation Overview

- Exports information saved to database during processing
- Files stored in temporary location until downloaded
- Memory optimization: only CSV format supported for async (XLSX requires full memory allocation)
- Custom controller overrides standard export routes

### Configuration Steps

#### Controller Override
```yaml
# Route configuration
    _controller: App\Controller\Backend\CustomExportController::exportAction
```

#### Grid Action
```php
// Add to grid configuration
CustomExportAction::create('_route_name_')
// Use correct route name for target entity
```

#### Service Configuration
```yaml
# Modify exporter service for entity
app.exporter._entity_type_.csv:
    class: App\Exporter\Resource\AsyncResourceExporter
    arguments:
        # ... existing arguments
        - "@app.repository._entity_type_"  # Add repository as final parameter
```

### Extra commands

Generate export for entity with a maker command:

for details view `src/Maker/ExportEntityMaker.php`
```
php bin/console make:export-entity app_customer

Enter the entity properties you want to export (comma-separated):
Example: id,name,email,createdAt

Properties [id]:
> id,firstName,lastName,email

```
