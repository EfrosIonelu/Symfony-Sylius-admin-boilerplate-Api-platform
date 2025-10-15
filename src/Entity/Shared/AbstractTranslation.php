<?php

namespace App\Entity\Shared;

use App\Entity\Traits\EntityAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Model\AbstractTranslation as BaseAbstractTranslation;

#[ORM\MappedSuperclass]
abstract class AbstractTranslation extends BaseAbstractTranslation implements TranslationInterface
{
    use EntityAwareTrait;

    #[ORM\Column(length: 4, nullable: true)]
    protected ?string $locale = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
