<?php

namespace App\Repository;

use App\Entity\Example;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @method Example|null find($id, $lockMode = null, $lockVersion = null)
 * @method Example|null findOneBy(array $criteria, array $orderBy = null)
 * @method Example[]    findAll()
 * @method Example[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExampleRepository extends EntityRepository
{
}
