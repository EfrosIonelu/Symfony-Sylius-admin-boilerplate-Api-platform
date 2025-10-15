<?php

namespace App\Grid\Cms;

use App\Entity\Cms\Media;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class MediaGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_media';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 20, 50])
            ->addField(
                StringField::create('id')
                    ->setLabel('id')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('filePath', 'admin/grid/field/media_file.html.twig')
                    ->setPath('.')
                    ->setLabel('File')
                    ->setSortable(false)
            )
            ->addField(
                StringField::create('originalName')
                    ->setLabel('OriginalName')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('size')
                    ->setLabel('Size')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('mimeType')
                    ->setLabel('MimeType')
                    ->setSortable(true)
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addOrderBy('id', 'desc')
        ;
    }

    public function getResourceClass(): string
    {
        return Media::class;
    }
}
