<?php

namespace App\Exporter\Plugin;

use App\Entity\Cms\Config;
use Sylius\Component\Resource\Model\ResourceInterface;

class ConfigResourcePlugin extends AbstractResourcePlugin
{
    protected function addGeneralData(ResourceInterface $resource): void
    {
        if (!$resource instanceof Config) {
            return;
        }

        $this->addDataForResource($resource, 'keyword', $resource->getKeyword());
        $this->addDataForResource($resource, 'value', $resource->getValue());
    }
}
