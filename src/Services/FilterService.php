<?php

namespace App\Services;

use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;

class FilterService
{
    protected array $map = [];

    public function __construct(protected readonly iterable $filters)
    {
    }

    public function getMap(): array
    {
        if (empty($this->map)) {
            foreach ($this->filters as $filter) {
                if (is_a($filter, ConfigurableFilterInterface::class)) {
                    $this->map[$filter->getType()] = $filter->getFormType();
                }
            }
        }

        return $this->map;
    }
}
