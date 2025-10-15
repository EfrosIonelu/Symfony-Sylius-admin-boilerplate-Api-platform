<?php

namespace App\Entity\Shared;

use Sylius\Component\Resource\Model\ResourceInterface;

interface EntityInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function setId(?int $id): void;

    public function getIdentifier(): string;
}
