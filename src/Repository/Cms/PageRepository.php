<?php

namespace App\Repository\Cms;

use App\Entity\Cms\Page;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function findOneBySlug(string $slug): ?Page
    {
        try {
            return $this->createQueryBuilder('p')
                ->innerJoin('p.translations', 't')
                ->andWhere('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Exception $e) {
            return null;
        }
    }
}
