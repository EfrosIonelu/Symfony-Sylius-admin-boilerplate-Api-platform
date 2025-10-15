<?php

declare(strict_types=1);

namespace App\Grid\Action;

use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class ExportAction
{
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('export', 'export');
        $action->setLabel('app.ui.export');
        $action->setOptions($options);

        return $action;
    }
}
