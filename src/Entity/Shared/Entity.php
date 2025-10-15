<?php

namespace App\Entity\Shared;

use App\Entity\Traits\EntityAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\MappedSuperclass]
abstract class Entity implements ResourceInterface
{
    use EntityAwareTrait;
}
