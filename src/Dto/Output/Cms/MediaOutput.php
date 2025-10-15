<?php

namespace App\Dto\Output\Cms;

use App\Dto\Factory\ApiOutputFactory;
use App\Dto\Output\AbstractOutput;
use App\Entity\Cms\Media;
use Symfony\Component\Serializer\Attribute\Groups;

class MediaOutput extends AbstractOutput
{
    public function __construct(
        private readonly Media $entity,
        ApiOutputFactory $apiOutputFactory,
        array $context,
    ) {
        parent::__construct($apiOutputFactory, $context);
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getId(): ?int
    {
        return $this->entity->getId();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getFilepath(): ?string
    {
        return $this->entity->getFilepath();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getMimeType(): ?string
    {
        return $this->entity->getMimeType();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getSize(): ?int
    {
        return $this->entity->getSize();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getOriginalName(): ?string
    {
        return $this->entity->getOriginalName();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getExtension(): ?string
    {
        return $this->entity->getExtension();
    }

    #[Groups([
        'media:list_read', 'media:item_read',
    ])]
    public function getType(): ?string
    {
        return $this->entity->getType();
    }
}
