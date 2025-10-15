<?php

namespace App\Dto\Output;

use App\Dto\Factory\ApiOutputFactory;

abstract class AbstractOutput
{
    public function __construct(
        protected readonly ApiOutputFactory $apiOutputFactory,
        protected array $context,
    ) {
    }
}
