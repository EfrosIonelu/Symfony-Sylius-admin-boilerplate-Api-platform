<?php

namespace App\Entity\Cms;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface PageTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getName(): ?string;

    public function getSlug(): ?string;

    public function setName(?string $name): void;

    public function getContent(): ?string;

    public function setContent(?string $content): void;
}
