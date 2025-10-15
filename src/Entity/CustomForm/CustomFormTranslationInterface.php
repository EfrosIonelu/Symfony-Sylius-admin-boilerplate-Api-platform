<?php

namespace App\Entity\CustomForm;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface CustomFormTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;
}
