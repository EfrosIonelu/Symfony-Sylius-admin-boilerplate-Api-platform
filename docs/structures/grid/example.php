<?php

namespace App\Grid\Example;

use App\Entity\Example\ExampleEntity;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

/**
 * Example Grid Structure for Sylius Grid Bundle
 * 
 * This demonstrates the complete structure of a grid with:
 * - Field definitions for displaying data
 * - Filters for searching and filtering data
 * - Actions for CRUD operations
 * - Sorting and pagination capabilities
 */
final class ExampleGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // Inject services if required (repositories, translators, etc.)
    }

    /**
     * Unique grid identifier - used in templates and routing
     */
    public static function getName(): string
    {
        return 'app_example';
    }

    /**
     * Main grid configuration method
     */
    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // FIELDS - Define columns to display in the grid
            ->addField(
                StringField::create('title')
                    ->setLabel('Title')                // Column header label
                    ->setSortable(true)               // Enable sorting
                    ->setPath('title')                // Entity property path
            )
            ->addField(
                StringField::create('description')
                    ->setLabel('Description')
                    ->setSortable(false)              // Disable sorting for long text
                    ->setPath('description')
            )
            ->addField(
                TwigField::create('status', '@Admin/Grid/Field/status.html.twig')
                    ->setLabel('Status')
                    ->setSortable(true)
                    ->setPath('isActive')             // Use boolean field for status
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('Created At')
                    ->setSortable(true)
                    ->setFormat('Y-m-d H:i:s')       // Date format
            )

            // FILTERS - Define search/filter options
            ->addFilter(
                StringFilter::create('title')
                    ->setLabel('Search Title')
            )
            ->addFilter(
                BooleanFilter::create('isActive')
                    ->setLabel('Active Status')
            )
            ->addFilter(
                DateFilter::create('createdAt')
                    ->setLabel('Created Date')
            )

            // ACTION GROUPS - Define available actions

            // Main actions (usually create new items)
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()           // "Create New" button
                )
            )

            // Item actions (actions for individual rows)
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),            // View details
                    UpdateAction::create(),          // Edit item
                    DeleteAction::create()           // Delete item
                )
            )

            // Bulk actions (actions for multiple selected items)
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()           // Bulk delete
                )
            )
        ;
    }

    /**
     * Return the entity class this grid manages
     */
    public function getResourceClass(): string
    {
        return ExampleEntity::class;
    }
}

/**
 * AVAILABLE FIELD TYPES:
 * 
 * StringField::create('property')              // Simple text display
 * TwigField::create('name', 'template.twig')   // Custom template rendering
 * DateTimeField::create('property')            // Date/time formatting
 * IntegerField::create('property')             // Number formatting
 * BooleanField::create('property')             // Yes/No display
 * 
 * AVAILABLE FILTER TYPES:
 * 
 * StringFilter::create('property')             // Text search
 * BooleanFilter::create('property')            // True/False filter
 * DateFilter::create('property')               // Date range filter
 * NumericRangeFilter::create('property')       // Number range filter
 * EntityFilter::create('property', EntityClass::class) // Related entity filter
 * 
 * AVAILABLE ACTION TYPES:
 * 
 * CreateAction::create()                       // Create new item
 * ShowAction::create()                         // View item details
 * UpdateAction::create()                       // Edit item
 * DeleteAction::create()                       // Delete item
 * 
 * FIELD CONFIGURATION OPTIONS:
 * 
 * ->setLabel('Custom Label')                   // Column header text
 * ->setSortable(true|false)                    // Enable/disable sorting
 * ->setPath('property.subProperty')            // Nested property access
 * ->setOptions(['key' => 'value'])             // Additional options
 * 
 * CUSTOM FIELD TEMPLATES:
 * 
 * For TwigField, create templates in templates/admin/grid/field/
 * Example: templates/admin/grid/field/custom_status.html.twig
 * 
 * GRID REGISTRATION:
 * 
 * Grids are automatically registered if they implement ResourceAwareGridInterface
 * and are placed in src/Grid/ directory with proper namespace.
 * 
 * USAGE IN ENTITY:
 * 
 * Add to your entity's #[AsResource] attribute:
 * 
 * #[AsResource(
 *     section: 'admin',
 *     templatesDir: '@SyliusAdminUi/crud',
 *     routePrefix: '/admin',
 *     grid: 'app_example',  // Reference to this grid
 *     operations: [...]
 * )]
 */