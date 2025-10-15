<?php

namespace App\Exporter\Resource\Interfaces;

use Sylius\Component\Resource\Repository\RepositoryInterface;

interface HasRepositoryDefinedInterface
{
    public function getRepository(): RepositoryInterface;
}
