<?php

namespace App\Exporter\Custom;

class ExporterFactory
{
    public const DEFAULT_RESOURCE_EXPORTER = 'app.default_resource_exporter.csv';

    public function __construct(private iterable $exporters = [])
    {
    }

    public function getByName(string $name): ?ExporterServiceInterface
    {
        foreach ($this->exporters as $exporter) {
            if ($exporter->getName() == $name) {
                return $exporter;
            }
        }

        foreach ($this->exporters as $exporter) {
            if (self::DEFAULT_RESOURCE_EXPORTER == $exporter->getName()) {
                return $exporter;
            }
        }

        throw new \Exception("Exporter with name '{$name}' not found");
    }
}
