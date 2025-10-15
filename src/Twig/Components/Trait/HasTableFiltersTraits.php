<?php

namespace App\Twig\Components\Trait;

use App\Services\FilterService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

trait HasTableFiltersTraits
{
    use ComponentWithFormTrait;

    protected readonly FilterService $filterService;

    protected function instantiateForm(): FormInterface
    {
        $builder = $this->container->get('form.factory')->createNamedBuilder('criteria', FormType::class, [
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);

        $this->buildFilterForm($builder);

        return $builder->getForm();
    }

    protected function buildFilterForm(FormBuilder $builder): void
    {
        $gridView = $this->getGridView();
        $enabledFilters = $gridView->getDefinition()->getEnabledFilters();

        foreach ($enabledFilters as $filter) {
            if (!$filter->isEnabled()) {
                continue;
            }

            $filterName = $filter->getName();
            $filterOptions = $filter->getOptions();
            $filterType = $filter->getType();

            // Map Sylius filter types to form types
            $formType = $this->getFormTypeForFilter($filterType);

            $builder->add($filterName, $formType, array_merge($filterOptions, [
                'required' => false,
            ]));
        }
    }

    protected function getFormTypeForFilter(string $filterType): ?string
    {
        $map = $this->filterService->getMap();
        if (isset($map[$filterType])) {
            return $map[$filterType];
        }

        // sylius filters add them only after testing
        return match ($filterType) {
            'select' => \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,
            default => null,
        };
    }

    #[LiveAction]
    public function applyFilters(): void
    {
        // Reset the grid view to force regeneration with new filter criteria
        $this->gridView = null;
    }

    #[LiveAction]
    public function resetFilters(): void
    {
        $this->formValues = [];
        $this->gridView = null;
        $this->resetForm();
    }

    #[ExposeInTemplate(name: 'filterParameters', getter: 'getFilterParameters')]
    protected array $filterParameters = [];

    public function getFilterParameters(): array
    {
        return !empty($this->formValues) ? ['criteria' => $this->formValues] : [];
    }
}
