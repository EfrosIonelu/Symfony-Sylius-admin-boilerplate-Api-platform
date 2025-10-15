<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Factory\ApiOutputFactory;
use App\Entity\Shared\EntityInterface;
use App\Entity\Shared\TranslationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MainEntityProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private readonly ProcessorInterface $innerProcessor,
        private readonly ApiOutputFactory $apiOutputFactory,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->innerProcessor->process($data, $operation, $uriVariables, $context);

        if (!is_a($data, EntityInterface::class) && !is_a($data, TranslationInterface::class)) {
            throw new \Exception('MainEntityProcessor | Invalid entity type');
        }

        return $this->apiOutputFactory->create($data, $context);
    }
}
