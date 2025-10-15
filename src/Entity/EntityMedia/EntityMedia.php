<?php

namespace App\Entity\EntityMedia;

use App\Entity\Cms\Media;
use App\Entity\Shared\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_cms_entity_media')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'page' => PageMedia::class,
])]
abstract class EntityMedia extends Entity
{
    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    #[ORM\Column(name: 'entity_id', type: Types::INTEGER, nullable: true)]
    protected ?int $entityId = null;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: 'entityMedias')]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id', nullable: false)]
    protected Media $media;

    abstract public function getType(): string;

    abstract public function getEntity(): ?Entity;

    abstract public function setEntity(?Entity $entity): void;

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(?string $zone): void
    {
        $this->zone = $zone;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): void
    {
        $media->addEntityMedia($this);
        $this->media = $media;
    }
}
