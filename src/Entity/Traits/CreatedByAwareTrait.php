<?php

namespace App\Entity\Traits;

use App\Entity\User\AppUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait CreatedByAwareTrait
{
    #[ORM\ManyToOne(targetEntity: AppUser::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Gedmo\Blameable(on: 'create')]
    protected ?AppUser $createdBy = null;

    public function getCreatedBy(): ?AppUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?AppUser $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}
