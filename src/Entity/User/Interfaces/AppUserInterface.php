<?php

namespace App\Entity\User\Interfaces;

use Sylius\Component\User\Model\UserInterface;

interface AppUserInterface extends UserInterface
{
    public function setCustomer(?AppCustomerInterface $customer): void;
}
