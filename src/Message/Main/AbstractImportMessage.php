<?php

namespace App\Message\Main;

abstract class AbstractImportMessage
{
    public function __construct(public array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }
}
