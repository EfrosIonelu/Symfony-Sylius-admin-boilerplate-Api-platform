<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

// Used for sorting
trait OrderAwareTrait
{
    #[ORM\Column(name: '`order`', type: 'integer', nullable: true)]
    private ?int $order = 0;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }
}
