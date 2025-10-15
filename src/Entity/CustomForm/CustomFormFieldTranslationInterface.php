<?php

namespace App\Entity\CustomForm;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface CustomFormFieldTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getLabel(): ?string;

    public function setLabel(?string $label): void;
}
