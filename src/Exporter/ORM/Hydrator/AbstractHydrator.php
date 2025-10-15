<?php

namespace App\Exporter\ORM\Hydrator;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\ORM\Hydrator\HydratorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

abstract class AbstractHydrator implements HydratorInterface
{
    public function __construct(protected RepositoryInterface $repository)
    {
    }

    /**
     * @return ResourceInterface[]
     */
    public function getHydratedResources(array $idsToExport): array
    {
        if (!$this->repository instanceof EntityRepository) {
            return $this->repository->findBy(['id' => $idsToExport]);
        }

        $query = $this->findQb($idsToExport)->getQuery();

        return $this->enableEagerLoading($query)->getResult();
    }

    protected function findQb(array $idsToExport): QueryBuilder
    {
        $repository = $this->repository;

        if (false === is_a($repository, EntityRepository::class)) {
            throw new \Exception('Repository must be an instance of EntityRepository');
        }

        return $repository->createQueryBuilder('o')
            ->andWhere('o.id IN (:exportIds)')
            ->setParameter('exportIds', $idsToExport);
    }

    protected function enableEagerLoading(Query $query): Query
    {
        return $query;
    }
}
