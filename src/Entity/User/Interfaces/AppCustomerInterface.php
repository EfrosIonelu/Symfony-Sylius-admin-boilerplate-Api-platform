<?php

namespace App\Entity\User\Interfaces;

use App\Entity\User\AppUser;
use Sylius\Component\Customer\Model\CustomerInterface;

interface AppCustomerInterface extends CustomerInterface
{
    public function setUser(?AppUser $user): void;
}
