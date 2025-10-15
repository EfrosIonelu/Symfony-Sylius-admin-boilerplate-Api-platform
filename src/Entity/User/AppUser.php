<?php

namespace App\Entity\User;

use App\Entity\User\Interfaces\AppCustomerInterface;
use App\Entity\User\Interfaces\AppUserInterface;
use App\Repository\User\AppUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Odiseo\SyliusRbacPlugin\Entity\AdministrationRoleAwareInterface;
use Odiseo\SyliusRbacPlugin\Entity\AdministrationRoleInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\User\Model\User as BaseUser;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[ORM\Table(name: 'app_user')]
class AppUser extends BaseUser implements AppUserInterface, AdministrationRoleAwareInterface, AdminUserInterface
{
    #[ORM\OneToOne(targetEntity: AppCustomer::class, mappedBy: 'user', cascade: ['persist'])]
    protected ?AppCustomerInterface $customer = null;

    #[ManyToOne(targetEntity: AppAdministrationRole::class)]
    #[ORM\JoinColumn(name: 'administration_role_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private $administrationRole;

    public function getCustomer(): ?AppCustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?AppCustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getFirstName(): ?string
    {
        return null;
    }

    public function setFirstName(?string $firstName): void
    {
    }

    public function getLastName(): ?string
    {
        return null;
    }

    public function setLastName(?string $lastName): void
    {
    }

    public function getLocaleCode(): ?string
    {
        return null;
    }

    public function setLocaleCode(?string $code): void
    {
    }

    public function getAvatar(): ?ImageInterface
    {
        return null;
    }

    public function setAvatar(?ImageInterface $avatar)
    {
    }

    public function getImage(): ?ImageInterface
    {
        return null;
    }

    public function setImage(?ImageInterface $image): void
    {
    }

    public function getAdministrationRole(): ?AdministrationRoleInterface
    {
        return $this->administrationRole;
    }

    public function setAdministrationRole(?AdministrationRoleInterface $administrationRole): void
    {
        $this->administrationRole = $administrationRole;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
        $this->usernameCanonical = $username;
    }
}
