<?php

namespace App\Entity\Traits;

use App\Entity\User\AppUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait UpdatedByAwareTrait
{
    #[ORM\ManyToOne(targetEntity: AppUser::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Gedmo\Blameable(on: 'update')]
    protected ?AppUser $updatedBy = null;

    public function getUpdatedBy(): ?AppUser
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?AppUser $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }
}
