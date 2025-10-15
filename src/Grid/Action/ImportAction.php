<?php

declare(strict_types=1);

namespace App\Grid\Action;

use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ActionInterface;

final class ImportAction
{
    public static function create(array $options = []): ActionInterface
    {
        $action = Action::create('import', 'import');
        $action->setLabel('app.ui.import');
        $action->setOptions($options);

        return $action;
    }
}
