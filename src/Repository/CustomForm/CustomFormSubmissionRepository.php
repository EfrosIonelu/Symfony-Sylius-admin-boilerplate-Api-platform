<?php

namespace App\Repository\CustomForm;

use App\Entity\CustomForm\CustomFormSubmission;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @method CustomFormSubmission|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomFormSubmission|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomFormSubmission[]    findAll()
 * @method CustomFormSubmission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFormSubmissionRepository extends EntityRepository implements RepositoryInterface
{
}
