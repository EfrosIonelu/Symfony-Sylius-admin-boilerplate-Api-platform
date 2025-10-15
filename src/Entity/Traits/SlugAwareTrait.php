<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait SlugAwareTrait // @phpstan-ignore-line
{
    #[ORM\Column(type: 'string', nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]
    protected ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
