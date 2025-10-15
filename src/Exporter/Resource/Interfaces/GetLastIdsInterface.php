<?php

namespace App\Exporter\Resource\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface GetLastIdsInterface
{
    public function getLastIds(?int $lastId, int $limit): QueryBuilder;
}
