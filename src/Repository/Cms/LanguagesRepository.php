<?php

namespace App\Repository\Cms;

use App\Entity\Cms\Languages;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Languages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Languages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Languages[]    findAll()
 * @method Languages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguagesRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Languages::class);
    }
}
