<?php

namespace App\Importer\Config;

use App\Entity\Cms\Config;
use App\Importer\Main\AbstractProcessor;

class ConfigProcessor extends AbstractProcessor
{
    public function process(array $data): void
    {
        $this->metadataValidator->validateHeaders($this->headerKeys, $data);

        $config = $this->getConfig($data['keyword']);
        if (null === $config) {
            $config = new Config();
            $config->setKeyword($data['keyword']);
        }

        $config->setValue($data['value']);
        $this->entityManager->persist($config);
    }

    public function getConfig(string $keyword): ?Config
    {
        return $this->entityManager->getRepository(Config::class)->findOneBy(['keyword' => $keyword]);
    }
}
