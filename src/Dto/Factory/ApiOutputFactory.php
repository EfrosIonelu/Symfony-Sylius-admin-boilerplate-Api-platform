<?php

namespace App\Dto\Factory;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Dto\Output\Cms\ConfigOutput;
use App\Dto\Output\Cms\MediaOutput;
use App\Entity\Cms\Config;
use App\Entity\Cms\Media;
use App\Entity\Shared\EntityInterface;
use App\Entity\Shared\TranslationInterface;
use Doctrine\Common\Collections\Collection;

class ApiOutputFactory
{
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
    ) {
    }

    public function getIriConverter(): IriConverterInterface
    {
        return $this->iriConverter;
    }

    public function create(EntityInterface|TranslationInterface|null $entity, array $context = []): mixed
    {
        if (null === $entity) {
            return null;
        }

        return match (true) {
            // CMS
            $entity instanceof Config => new ConfigOutput($entity, $this, $context),
            $entity instanceof Media => new MediaOutput($entity, $this, $context),
            default => throw new \Exception('Not implemented'),
        };
    }

    public function createCollection(Collection $entities, array $context = []): array
    {
        if (0 === $entities->count()) {
            return [];
        }

        $response = [];
        foreach ($entities as $entity) {
            $response[] = $this->create($entity, $context);
        }

        return $response;
    }
}
