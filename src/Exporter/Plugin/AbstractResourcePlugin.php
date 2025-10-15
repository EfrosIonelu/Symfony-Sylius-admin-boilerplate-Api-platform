<?php

namespace App\Exporter\Plugin;

use Doctrine\ORM\EntityManagerInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\ORM\Hydrator\HydratorInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\ResourcePlugin;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

abstract class AbstractResourcePlugin extends ResourcePlugin
{
    public function __construct(
        RepositoryInterface $repository,
        PropertyAccessorInterface $propertyAccessor,
        EntityManagerInterface $entityManager,
        private readonly HydratorInterface $entityHydrator,
    ) {
        parent::__construct($repository, $propertyAccessor, $entityManager);
    }

    public function init(array $idsToExport): void
    {
        parent::init($idsToExport);

        foreach ($this->resources as $resource) {
            $this->addGeneralData($resource);
        }
    }

    protected function findResources(array $idsToExport): array
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger();

        return $this->entityHydrator->getHydratedResources($idsToExport);
    }

    abstract protected function addGeneralData(ResourceInterface $resource): void;
}
