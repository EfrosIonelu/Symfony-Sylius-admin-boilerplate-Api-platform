<?php

namespace App\Repository\User;

use App\Entity\User\AppAdministrationRole;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppAdministrationRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppAdministrationRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppAdministrationRole[]    findAll()
 * @method AppAdministrationRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppAdministrationRoleRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppAdministrationRole::class);
    }
}
