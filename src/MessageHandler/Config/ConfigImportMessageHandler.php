<?php

namespace App\MessageHandler\Config;

use App\Message\Config\ConfigImportMessage;
use App\MessageHandler\Main\AbstractImportMessageHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ConfigImportMessageHandler extends AbstractImportMessageHandler
{
    public function __invoke(ConfigImportMessage $importMessage): void
    {
        $data = $importMessage->getData();

        $this->import($data);
    }
}
