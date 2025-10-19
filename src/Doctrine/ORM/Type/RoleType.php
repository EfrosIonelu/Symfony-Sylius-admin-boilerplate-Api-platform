<?php

namespace App\Doctrine\ORM\Type;

class RoleType extends EnumType
{
    // frontend dashboard
    final public const ROLE_USER = 'ROLE_USER';

    // Admin dashboard
    final public const ROLE_ADMIN = 'ROLE_ADMIN';

    // organization api endpoints
    final public const ROLE_ORGANIZATION = 'ROLE_ORGANIZATION';

    // internal api endpoints
    final public const ROLE_INTERNAL = 'ROLE_INTERNAL';

    protected static string $name = 'roleType';
    protected static string $choicePrefix = 'role.type.';
    protected static string $choiceSuffix = '';
    protected static array $values = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_ORGANIZATION,
        self::ROLE_INTERNAL,
    ];
}
