<?php

namespace App\Repository\CustomForm;

use App\Entity\CustomForm\FormSubmissionValues;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormSubmissionValues|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormSubmissionValues|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormSubmissionValues[]    findAll()
 * @method FormSubmissionValues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormSubmissionValuesRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormSubmissionValues::class);
    }
}
