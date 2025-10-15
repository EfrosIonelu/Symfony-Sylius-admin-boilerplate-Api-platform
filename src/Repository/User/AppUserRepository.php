<?php

namespace App\Repository\User;

use App\Entity\User\AppUser;
use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @method AppUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppUser[]    findAll()
 * @method AppUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppUserRepository extends BaseUserRepository implements UserRepositoryInterface
{
    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.customer', 'customer')
            ->andWhere('customer.emailCanonical = :email OR o.username = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
