<?php

namespace App\Entity\Log;

use App\Entity\Traits\CreatedByAwareTrait;
use App\Repository\Log\LogEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

#[ORM\Entity(repositoryClass: LogEntryRepository::class)]
#[ORM\Table(name: 'app_entries_log')]
#[ORM\Index(name: 'log_class_lookup_idx', columns: ['object_class'])]
#[ORM\Index(name: 'log_date_lookup_idx', columns: ['logged_at'])]
#[ORM\Index(name: 'log_user_lookup_idx', columns: ['username'])]
#[ORM\Index(name: 'log_version_lookup_idx', columns: ['object_id', 'object_class', 'version'])]
class LogEntry extends AbstractLogEntry
{
    use CreatedByAwareTrait;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    protected $data;
}
