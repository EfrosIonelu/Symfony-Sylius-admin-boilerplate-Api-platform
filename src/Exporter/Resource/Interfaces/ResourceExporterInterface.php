<?php

namespace App\Exporter\Resource\Interfaces;

use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\PluginPoolInterface;

interface ResourceExporterInterface extends HasRepositoryDefinedInterface
{
    public function getResourceKeys(): array;

    public function getPluginPool(): PluginPoolInterface;
}
