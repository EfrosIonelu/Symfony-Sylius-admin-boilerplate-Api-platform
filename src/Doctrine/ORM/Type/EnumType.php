<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class EnumType extends Type implements EnumTypeInterface
{
    protected static string $name;
    protected static string $choicePrefix = '';
    protected static string $choiceSuffix = '';
    protected static array $values = [];

    public function getName(): string
    {
        return static::$name;
    }

    public static function getValues(): array
    {
        return static::$values;
    }

    public static function getChoice(string $value): string
    {
        return static::$choicePrefix.strtolower($value).static::$choiceSuffix;
    }

    public static function getChoices(?array $values = null): array
    {
        $choices = [];
        $values = $values ?: static::$values;
        foreach ($values as $value) {
            $choice = static::getChoice($value);
            $choices[$choice] = $value;
        }

        return $choices;
    }

    public static function getSelectable(?array $values = null): array
    {
        $choices = [];

        foreach ($values as $value) {
            $choices[] = [
                'value' => $value,
                'label' => $value,
            ];
        }

        return $choices;
    }

    public static function getTranslatedValues(TranslatorInterface $translator, string $prefix = ''): array
    {
        $choices = [];
        foreach (static::$values as $value) {
            $choice = static::getChoice($value);
            $choices[$value] = $translator->trans($prefix.$choice);
        }

        return $choices;
    }

    public static function getTranslatedValue(TranslatorInterface $translator, string $value, string $prefix = ''): string
    {
        $choice = static::getChoice($value);

        return $translator->trans($prefix.$choice);
    }

    public function getSqlDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = array_map(fn ($val) => "'".$val."'", static::$values);

        return 'ENUM('.implode(', ', $values).") COMMENT '(DC2Type:".static::$name.")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!in_array($value, static::$values)) {
            throw new \InvalidArgumentException("Invalid '".static::$name."' value.");
        }

        return $value;
    }

    /**
     * Check if some string value exists in the array of ENUM values.
     *
     * @param string $value ENUM value
     */
    public static function isValueExist($value): bool
    {
        return in_array($value, static::getValues());
    }

    public static function valueExists($value): bool
    {
        return self::isValueExist($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
