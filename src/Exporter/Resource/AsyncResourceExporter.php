<?php

namespace App\Exporter\Resource;

use App\Exporter\Resource\Interfaces\ResourceExporterInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\PluginPoolInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\ResourceExporter;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Transformer\TransformerPoolInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Writer\WriterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class AsyncResourceExporter extends ResourceExporter implements ResourceExporterInterface
{
    private RepositoryInterface $repository;

    public function __construct(
        WriterInterface $writer,
        PluginPoolInterface $pluginPool,
        array $resourceKeys,
        ?TransformerPoolInterface $transformerPool,
        RepositoryInterface $repository,
    ) {
        parent::__construct($writer, $pluginPool, $resourceKeys, $transformerPool);
        $this->repository = $repository;
    }

    public function getResourceKeys(): array
    {
        return $this->resourceKeys;
    }

    public function getPluginPool(): PluginPoolInterface
    {
        return $this->pluginPool;
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
