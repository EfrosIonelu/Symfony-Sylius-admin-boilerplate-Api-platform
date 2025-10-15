<?php

namespace App\Entity\User;

use App\Entity\User\Interfaces\AppCustomerInterface;
use App\Form\Type\User\AppCustomerType;
use App\Grid\User\AppCustomerGrid;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\Customer as BaseCustomer;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Symfony\Component\Validator\Constraints as Assert;

#[AsResource(
    section: 'admin',
    formType: AppCustomerType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'app_customer',
    operations: [
        new Index(
            grid: AppCustomerGrid::class
        ),
        new Create(),
        new Update(),
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'app_customer')]
class AppCustomer extends BaseCustomer implements AppCustomerInterface
{
    #[ORM\OneToOne(targetEntity: AppUser::class, inversedBy: 'customer', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    protected ?AppUser $user = null;

    #[Assert\NotNull(groups: ['app_customer:edit'])]
    protected $email;

    #[Assert\NotNull(groups: ['app_customer:edit'])]
    protected $gender = CustomerInterface::UNKNOWN_GENDER;

    public function getUser(): ?AppUser
    {
        return $this->user;
    }

    public function setUser(?AppUser $user): void
    {
        $this->user = $user;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
        $this->emailCanonical = $email;
    }
}
