<?php

namespace App\Twig;

use App\Twig\Trait\GridTrait;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    use GridTrait;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly GridProviderInterface $gridProvider,
        private readonly GridViewFactoryInterface $gridViewFactory,
    ) {
    }

    public function getFunctions(): array
    {
        $names = [
            'get_grid_view_by_name' => 'getGridViewByName',
        ];

        $functions = [];
        foreach ($names as $twig => $local) {
            $functions[] = new TwigFunction($twig, [$this, $local], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    public function getGridViewByName(
        string $name,
        array $extraParameters = [],
        array $disableFilters = [],
        array $disabledFields = [],
    ): GridViewInterface {
        $request = $this->requestStack->getCurrentRequest();

        $grid = $this->gridProvider->get($name);

        $grid = $this->getApplyParametersToGrid(
            $grid,
            $disableFilters,
            $disabledFields,
            true
        );

        $parameters = new Parameters(array_merge($request->query->all(), $extraParameters));

        return $this->gridViewFactory->create($grid, $parameters);
    }
}
