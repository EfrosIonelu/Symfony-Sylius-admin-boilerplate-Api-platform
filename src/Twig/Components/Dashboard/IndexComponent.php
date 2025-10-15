<?php

namespace App\Twig\Components\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class IndexComponent extends AbstractController
{
    use DefaultActionTrait;

    #[ExposeInTemplate(name: 'number', getter: 'getNumber')]
    private $number = 1;

    public function getNumber(): int
    {
        return $this->number;
    }

    #[LiveAction]
    public function increment(#[LiveArg] int $currentNumber): void
    {
        $this->number = $currentNumber + 1;
    }
}
