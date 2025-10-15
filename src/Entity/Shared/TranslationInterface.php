<?php

namespace App\Entity\Shared;

interface TranslationInterface
{
    public function getLocale(): ?string;

    public function setLocale(?string $locale): void;

    public function getId(): ?int;

    public function setId(?int $id): void;

    public function getIdentifier(): string;
}
