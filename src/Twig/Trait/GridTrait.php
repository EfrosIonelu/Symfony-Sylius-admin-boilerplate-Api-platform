<?php

namespace App\Twig\Trait;

use Sylius\Component\Grid\Definition\Grid;

trait GridTrait
{
    public function getApplyParametersToGrid(
        Grid $grid,
        array $disableFilters = [],
        array $disabledFields = [],
        bool $disableActions = false,
    ): Grid {
        if (!empty($disableFilters)) {
            $filters = $grid->getFilters();
            foreach ($filters as $filter) {
                if (in_array($filter->getName(), $disableFilters)) {
                    $filter->setEnabled(false);
                }
            }
        }

        if (!empty($disabledFields)) {
            $fields = $grid->getFields();
            foreach ($fields as $field) {
                if (in_array($field->getName(), $disabledFields)) {
                    $field->setEnabled(false);
                }
            }
        }

        if ($disableActions && $grid->hasActionGroup('item')) {
            $actions = $grid->getActions('item');
            foreach ($actions as $action) {
                $action->setEnabled(false);
            }
        }

        return $grid;
    }
}
