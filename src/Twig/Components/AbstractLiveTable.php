<?php

namespace App\Twig\Components;

use App\Services\FilterService;
use App\Twig\Components\Trait\HasLiveTableHeadersTrait;
use App\Twig\Components\Trait\HasTableFiltersTraits;
use App\Twig\Trait\GridTrait;
use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

abstract class AbstractLiveTable extends AbstractController
{
    use HasLiveTableHeadersTrait;
    use HasTableFiltersTraits;
    use GridTrait;
    use ComponentToolsTrait;

    public function __construct(
        protected readonly GridViewFactoryInterface $gridViewFactory,
        #[Autowire(service: 'sylius.grid.service_grid_provider')]
        protected readonly GridProviderInterface $gridProvider,
        protected readonly FilterService $filterService,
    ) {
    }

    abstract protected function getGridName(): string;

    #[ExposeInTemplate(name: 'gridView', getter: 'getGridView')]
    protected ?GridViewInterface $gridView = null;

    #[ExposeInTemplate(name: 'title', getter: 'getTitle')]
    protected string $title = '';

    #[LiveProp(writable: false)]
    public int $numberOfResets = 0;

    public function getGridView(): GridViewInterface
    {
        if (null === $this->gridView) {
            $grid = $this->gridProvider->get($this->getGridName());

            $this->getApplyParametersToGrid(
                $grid,
                $this->getDisabledFilters(),
                $this->getDisabledColumns(),
                true,
            );

            $parameters = new Parameters(array_merge([
                'page' => $this->page,
            ], $this->getSortingParameters(), $this->getFilterParameters()));

            $this->gridView = $this->gridViewFactory->create($grid, $parameters);

            $data = $this->gridView->getData();
            if (is_a($data, PagerfantaInterface::class)) {
                if ($this->limit !== $data->getMaxPerPage()) {
                    $this->gridView = new GridView($this->gridView->getData()->setMaxPerPage($this->limit), $grid, $parameters);
                }
            }

            $this->tableLoaded();
        }

        return $this->gridView;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDisabledFilters(): array
    {
        return [];
    }

    public function getDisabledColumns(): array
    {
        return [];
    }

    public function tableLoaded(): void
    {
        $this->dispatchBrowserEvent('table:loaded');
    }
}
