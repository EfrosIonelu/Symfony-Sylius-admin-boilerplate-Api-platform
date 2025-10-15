<?php

namespace App\Twig\Components\Dashboard;

use App\Twig\Components\AbstractLiveTable;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class MediaTableComponent extends AbstractLiveTable
{
    use DefaultActionTrait;

    #[ExposeInTemplate(name: 'title', getter: 'getTitle')]
    protected string $title = 'app.ui.media_table';

    #[ExposeInTemplate(name: 'form', getter: 'getFormView')]
    private ?FormInterface $form = null; // @phpstan-ignore-line

    protected function getGridName(): string
    {
        return 'app_media';
    }
}
