<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Type;

interface EnumTypeInterface
{
    public function getName(): string;

    public static function getValues(): array;

    public static function getChoice(string $value): string;

    public static function getChoices(?array $values = null): array;

    public static function isValueExist(string $value): bool;
}
