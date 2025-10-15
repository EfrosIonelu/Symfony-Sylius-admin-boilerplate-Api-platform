<?php

namespace App\Entity\User;

use App\Grid\User\AdministrationRoleGrid;
use App\Repository\User\AppAdministrationRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Odiseo\SyliusRbacPlugin\Entity\AdministrationRole;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Index;

#[AsResource(
    section: 'admin',
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    operations: [
        new Index(
            routeName: 'sylius_rbac_admin_administration_role_index',
            grid: AdministrationRoleGrid::class
        ),
    ],
)]
#[ORM\Entity(repositoryClass: AppAdministrationRoleRepository::class)]
#[ORM\Table(name: 'app_administration_role')]
class AppAdministrationRole extends AdministrationRole
{
}
