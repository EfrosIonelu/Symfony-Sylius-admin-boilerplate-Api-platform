<?php

namespace App\Grid\User;

use App\Entity\User\AppCustomer;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AppCustomerGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_customer';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('id')
                    ->setLabel('ID')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('email')
                    ->setLabel('Email')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('lastName')
                    ->setLabel('Last Name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('firstName')
                    ->setLabel('First Name')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('user_roles', 'admin/grid/field/user_roles.html.twig')
                  ->setPath('.')
                  ->setLabel('Roles')
                  ->setSortable(false)
            )
            ->addField(
                StringField::create('user.administrationRole.name')
                    ->setLabel('app.user.administration_role.name')
                    ->setSortable(false)
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create()
                )
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return AppCustomer::class;
    }
}
