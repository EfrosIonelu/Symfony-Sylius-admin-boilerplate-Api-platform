<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Factory\ApiOutputFactory;
use App\Entity\Shared\EntityInterface;
use App\Entity\Shared\TranslationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MainEntityProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)]
        private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)]
        private ProviderInterface $itemProvider,
        private readonly ApiOutputFactory $apiOutputFactory,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $response = [];
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);
            foreach ($entities as $entity) {
                if (!is_a($entity, EntityInterface::class) && !is_a($entity, TranslationInterface::class)) {
                    throw new \Exception('MainEntityProvider | Invalid entity type');
                }
                $response[] = $this->apiOutputFactory->create($entity, $context);
            }

            return $response;
        }

        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (isset($context['api_denormalize']) && $context['api_denormalize']) {
            // when using the IRI main entity provider will be triggered
            return $entity;
        }

        if (!is_a($entity, EntityInterface::class) && !is_a($entity, TranslationInterface::class)) {
            throw new \Exception('MainEntityProvider | Invalid entity type');
        }

        return $this->apiOutputFactory->create($entity, $context);
    }
}
