<?php

namespace App\Exporter\Custom;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;

readonly class ExporterGridProvider
{
    public function __construct(
        protected GridProviderInterface $arrayGridProvider,
        protected GridProviderInterface $serviceGridProvider,
    ) {
    }

    public function get(string $gridName): Grid
    {
        try {
            return $this->arrayGridProvider->get($gridName);
        } catch (UndefinedGridException $e) {
        }

        try {
            return $this->serviceGridProvider->get($gridName);
        } catch (UndefinedGridException $e) {
        }

        throw new UndefinedGridException($gridName);
    }
}
