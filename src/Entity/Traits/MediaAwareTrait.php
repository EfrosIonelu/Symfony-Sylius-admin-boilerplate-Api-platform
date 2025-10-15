<?php

namespace App\Entity\Traits;

use App\Entity\EntityMedia\EntityMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait MediaAwareTrait
{
    /**
     * @var Collection<int, EntityMedia>
     */
    #[ORM\OneToMany(targetEntity: EntityMedia::class, mappedBy: 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $files;

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    public function addFile(EntityMedia $file): void
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file); // @phpstan-ignore-line
            $file->setEntity($this);
        }
    }

    public function removeFile(EntityMedia $file): void
    {
        $this->files->removeElement($file); // @phpstan-ignore-line
    }

    public function initializeMedia(): void
    {
        $this->files = new ArrayCollection();
    }
}
