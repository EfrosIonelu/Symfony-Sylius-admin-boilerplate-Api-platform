<?php

namespace App\Message\ExportFile;

class ExportFileMessage
{
    private int $externalId;

    public function __construct(int $externalId)
    {
        $this->externalId = $externalId;
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }
}
