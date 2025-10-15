<?php

namespace App\Grid\CustomForm;

use App\Entity\CustomForm\CustomFormSubmission;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class CustomFormSubmissionGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_custom_form_submission';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('id')
                    ->setLabel('app.ui.id')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('customForm.name')
                    ->setLabel('app.ui.form_name')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('app.ui.created_at')
                    ->setSortable(true)
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return CustomFormSubmission::class;
    }
}
