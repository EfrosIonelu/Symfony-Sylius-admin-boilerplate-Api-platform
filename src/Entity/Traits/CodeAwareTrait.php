<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CodeAwareTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }
}
