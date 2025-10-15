<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteAwareTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $deletedAt = null;

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
