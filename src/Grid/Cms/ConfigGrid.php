<?php

namespace App\Grid\Cms;

use App\Entity\Cms\Config;
use App\Grid\Action\CustomExportAction;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class ConfigGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public static function getName(): string
    {
        return 'app_config';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $criteria = $request ? $request->get('criteria', []) : [];

        $gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                StringField::create('keyword')
                    ->setLabel('Keyword')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('value')
                    ->setLabel('Value')
                    ->setSortable(true)
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                    CustomExportAction::create('app_export_data_config')
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            );
    }

    public function getResourceClass(): string
    {
        return Config::class;
    }
}
