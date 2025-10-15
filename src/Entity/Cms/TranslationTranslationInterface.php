<?php

namespace App\Entity\Cms;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface TranslationTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getValue(): ?string;

    public function setValue(?string $value): void;
}
