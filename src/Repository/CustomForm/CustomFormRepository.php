<?php

namespace App\Repository\CustomForm;

use App\Entity\CustomForm\CustomForm;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomForm[]    findAll()
 * @method CustomForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFormRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomForm::class);
    }
}
