<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnabledAwareTrait
{
    #[ORM\Column(type: 'boolean')]
    protected bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }
}
