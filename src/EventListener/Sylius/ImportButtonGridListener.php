<?php

declare(strict_types=1);

namespace App\EventListener\Sylius;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

final class ImportButtonGridListener
{
    public function __construct(
        private readonly string $resource,
    ) {
    }

    public function onSyliusGridAdmin(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();

        if (!$grid->hasActionGroup('main')) {
            $grid->addActionGroup(ActionGroup::named('main'));
        }

        $actionGroup = $grid->getActionGroup('main');

        if ($actionGroup->hasAction('import')) {
            return;
        }

        $explode = explode('.', $this->resource);
        $resource = \end($explode);
        $action = Action::fromNameAndType('import', 'import');
        $action->setLabel('app.ui.import');
        $action->setOptions([
            'icon' => 'material-symbols:upload',
            'route' => sprintf('app_backend_%s_import', $resource),
            'parameters' => [
                'resource' => $this->resource,
            ],
        ]);

        $actionGroup->addAction($action);
    }
}
