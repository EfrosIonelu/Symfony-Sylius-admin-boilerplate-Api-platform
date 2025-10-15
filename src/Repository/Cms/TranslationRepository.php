<?php

namespace App\Repository\Cms;

use App\Entity\Cms\Translation;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Translation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Translation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Translation[]    findAll()
 * @method Translation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslationRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    /**
     * Find translation by key.
     */
    public function findOneByKey(string $key): ?Translation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.key = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
