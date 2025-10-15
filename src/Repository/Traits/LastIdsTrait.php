<?php

namespace App\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

trait LastIdsTrait
{
    public function getLastIds(?int $lastId, int $limit): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->select('o.id')
            ->where('o.id > :lastId')
            ->setParameter('lastId', $lastId)
            ->setMaxResults($limit)
            ->orderBy('o.id', 'ASC');
    }
}
