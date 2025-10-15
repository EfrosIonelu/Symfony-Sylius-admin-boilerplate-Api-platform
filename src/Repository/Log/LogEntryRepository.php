<?php

namespace App\Repository\Log;

use App\Entity\Log\LogEntry;
use App\Repository\Shared\MainRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogEntry[]    findAll()
 * @method LogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogEntryRepository extends MainRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEntry::class);
    }
}
