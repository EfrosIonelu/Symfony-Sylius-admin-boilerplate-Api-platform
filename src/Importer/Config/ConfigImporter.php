<?php

namespace App\Importer\Config;

use App\Importer\Main\AbstractImporter;
use App\Message\Config\ConfigImportMessage;

class ConfigImporter extends AbstractImporter
{
    public function dispatch(array $data): void
    {
        $this->messageBus->dispatch(new ConfigImportMessage($data));
    }
}
