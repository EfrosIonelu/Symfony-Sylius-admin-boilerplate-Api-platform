<?php

declare(strict_types=1);

namespace App\Grid\Action;

use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class CustomExportAction
{
    public static function create(string $routeName): ActionInterface
    {
        $action = Action::create('export_custom', 'custom_export');
        $action->setLabel('app.ui.export');
        $action->setOptions([
            'exports' => [
                'csv' => [
                    'label' => 'CSV',
                    'route' => $routeName,
                    'parameters' => [
                        'format' => 'csv',
                    ],
                ],
            ],
        ]);

        return $action;
    }
}
