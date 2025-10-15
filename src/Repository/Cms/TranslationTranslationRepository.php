<?php

namespace App\Repository\Cms;

use App\Entity\Cms\TranslationTranslation;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TranslationTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranslationTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranslationTranslation[]    findAll()
 * @method TranslationTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslationTranslationRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslationTranslation::class);
    }

    /**
     * Find all translations for a specific locale.
     */
    public function findByLocale(string $locale): array
    {
        return $this->createQueryBuilder('tt')
            ->andWhere('tt.locale = :locale')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult();
    }
}
