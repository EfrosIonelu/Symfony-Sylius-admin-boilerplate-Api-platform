<?php

namespace App\Repository\Cms;

use App\Entity\Cms\Media;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * @return Media[] Returns an array of Media objects matching the search term
     */
    public function findByOriginalNameLike(string $searchTerm, ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.originalName LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%')
            ->orderBy('m.id', 'DESC');

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByOriginalNameLike(string $searchTerm): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.originalName LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
