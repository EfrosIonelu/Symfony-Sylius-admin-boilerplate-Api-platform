<?php

namespace App\Dto\Output\Cms;

use App\Dto\Factory\ApiOutputFactory;
use App\Dto\Output\AbstractOutput;
use App\Entity\Cms\Config;
use Symfony\Component\Serializer\Attribute\Groups;

class ConfigOutput extends AbstractOutput
{
    public function __construct(
        private readonly Config $entity,
        ApiOutputFactory $apiOutputFactory,
        array $context,
    ) {
        parent::__construct($apiOutputFactory, $context);
    }

    #[Groups([
        'config:list_read', 'config:item_read',
    ])]
    public function getId(): ?int
    {
        return $this->entity->getId();
    }

    #[Groups([
        'config:list_read', 'config:item_read',
    ])]
    public function getKeyword(): ?string
    {
        return $this->entity->getKeyword();
    }

    #[Groups([
        'config:list_read', 'config:item_read',
    ])]
    public function getValue(): ?string
    {
        return $this->entity->getValue();
    }
}
