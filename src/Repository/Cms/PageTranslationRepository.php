<?php

namespace App\Repository\Cms;

use App\Entity\Cms\PageTranslation;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageTranslation[]    findAll()
 * @method PageTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageTranslationRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageTranslation::class);
    }
}
