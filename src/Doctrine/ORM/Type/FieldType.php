<?php

namespace App\Doctrine\ORM\Type;

class FieldType extends EnumType
{
    final public const DATE = 'date';
    final public const SELECT = 'select';
    final public const TEXT = 'text';
    final public const TEXTAREA = 'textarea';
    final public const CHECKBOX = 'checkbox';
    final public const RADIO = 'radio';

    protected static string $name = 'fieldType';
    protected static string $choicePrefix = 'field.type.';
    protected static string $choiceSuffix = '';
    protected static array $values = [
        self::DATE,
        self::SELECT,
        self::TEXT,
        self::TEXTAREA,
        self::CHECKBOX,
        self::RADIO,
    ];
}
