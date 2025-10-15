# Extra Grid Functionality

## Standard Grid Rendering

Render a grid by name using the Sylius grid system:

```twig
{% set gridView = get_grid_view_by_name('app_languages') %}
{{ sylius_grid_render(gridView) }}
```

## Live Component Grids

Transform grids into interactive live components without page refreshes. All actions (pagination, sorting, filtering) execute via AJAX calls.

### Documentation Reference
- [Symfony UX Live Component Documentation](https://symfony.com/bundles/ux-live-component/current/index.html)
- Key implementation file: `src/Twig/Components/AbstractLiveTable.php`

### Creating a Live Grid Component

Create a new live component by extending `AbstractLiveTable`:

```php
<?php

namespace App\Twig\Components\Dashboard;

use App\Twig\Components\AbstractLiveTable;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class MediaTableComponent extends AbstractLiveTable
{
    use DefaultActionTrait;

    protected function getGridName(): string
    {
        return 'app_media';
    }
}
```

### Live Component Features

The `AbstractLiveTable` includes three behavioral traits:

- `src/Twig/Components/Trait/HasPaginationTrait.php` - Pagination behavior
- `src/Twig/Components/Trait/HasLiveTableHeadersTrait.php` - Sorting behavior  
- `src/Twig/Components/Trait/HasTableFiltersTraits.php` - Filtering behavior

### Template Usage

For live components, use only:

```twig
{% include 'shared/crud/index/content/grid/live_grid.html.html.twig' %}
```

## Adding a Custom Grid Filter

To add a custom filter to a Sylius grid, update the following files:
Documentation: [Grid Filters](https://stack.sylius.com/grid/index/custom_filter)

1. **Form Type**: Define the filter form type.
   - Example: `src/Form/Type/Filter/NumericFilterType.php`
2. **Filter Logic**: Implement how the filter is applied.
   - Example: `src/Grid/Filter/NumericFilter.php`
3. **Grid Configuration**: Register the filter and its Twig template.
   - Edit: `config/packages/sylius_grid.yaml`
   - Add the filter name and path to the Twig file.
4. **Twig Template**: Render the filter form.
   - Example:
     ```twig
     {% form_theme form '@SyliusBootstrapAdminUi/shared/form_theme.html.twig' %}
     {{ form_row(form, {'label': filter.label}) }}
     ```

Refer to the provided file examples for implementation details. This enables custom filtering in Sylius grids.
