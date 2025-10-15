<?php

namespace App\Grid\User;

use App\Entity\User\AppAdministrationRole;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AdministrationRoleGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_administration_role';
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
                StringField::create('name')
                    ->setLabel('app.ui.name')
                    ->setSortable(true)
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_rbac_admin_administration_role_create_view',
                            ],
                        ]),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create()
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_rbac_admin_administration_role_update_view',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                        ]),
                    //                    DeleteAction::create()
                )
            )
//            ->addActionGroup(
//                BulkActionGroup::create(
//                    DeleteAction::create()
//                )
//            )
        ;
    }

    public function getResourceClass(): string
    {
        return AppAdministrationRole::class;
    }
}
