<?php

namespace App\Factory;

use App\Entity\User\AppAdministrationRole;
use Odiseo\SyliusRbacPlugin\Entity\AdministrationRoleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class RbacFactory implements FactoryInterface
{
    public function createNew(): AdministrationRoleInterface
    {
        return new AppAdministrationRole();
    }
}
