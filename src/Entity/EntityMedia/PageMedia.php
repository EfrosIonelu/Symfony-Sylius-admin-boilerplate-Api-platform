<?php

namespace App\Entity\EntityMedia;

use App\Entity\Cms\Page;
use App\Entity\Shared\Entity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PageMedia extends EntityMedia
{
    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'files')]
    #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id')]
    private ?Page $entity = null;

    public function getPage(): ?Page
    {
        return $this->entity;
    }

    public function setPage(?Page $page): void
    {
        $this->entity = $page;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): void
    {
        if (!is_a($entity, Page::class)) {
            throw new \Exception('Entity must be a Page');
        }

        $this->entity = $entity;
    }

    public function getType(): string
    {
        return 'page';
    }
}
