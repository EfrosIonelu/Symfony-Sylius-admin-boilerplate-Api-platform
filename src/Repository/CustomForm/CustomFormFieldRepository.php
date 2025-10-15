<?php

namespace App\Repository\CustomForm;

use App\Entity\CustomForm\CustomFormField;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomFormField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomFormField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomFormField[]    findAll()
 * @method CustomFormField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFormFieldRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomFormField::class);
    }
}
