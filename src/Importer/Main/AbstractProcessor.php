<?php

declare(strict_types=1);

namespace App\Importer\Main;

use Doctrine\ORM\EntityManagerInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\MetadataValidatorInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

abstract class AbstractProcessor implements ResourceProcessorInterface
{
    public function __construct(
        protected FactoryInterface $resourceFactory,
        protected RepositoryInterface $resourceRepository,
        protected MetadataValidatorInterface $metadataValidator,
        protected EntityManagerInterface $entityManager,
        protected array $headerKeys,
    ) {
    }
}
