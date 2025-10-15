<?php

namespace App\Entity\Log;

use App\Entity\Shared\Entity;
use App\Entity\Traits\TimestampsAwareTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'app_export_file_log')]
#[ORM\Entity]
class ExportFileLog extends Entity
{
    use TimestampsAwareTrait;

    public const IN_PROGRESS = 'in_progress';
    public const ERROR = 'error';
    public const SUCCESS = 'success';

    #[ORM\Column(type: 'integer')]
    protected ?int $externalId = null;
    #[ORM\Column(type: 'string')]
    protected ?string $fileName = null;

    #[ORM\Column(type: 'string')]
    protected ?string $status = null;

    #[ORM\Column(type: 'string')]
    protected ?string $serviceRegistryName = null;

    #[ORM\Column(type: 'array', nullable: true)]
    protected ?array $additionalData = [];

    public function __construct(string $resource, string $format = 'csv')
    {
        $id = strtotime('now');
        $date = new \DateTime();

        $this->externalId = $id;
        $this->status = self::IN_PROGRESS;
        $this->fileName = $resource.'__'.$date->format('Y-m-d_H-i-s').'.'.$format;
        $this->serviceRegistryName = $resource.'.'.$format;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(?int $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getServiceRegistryName(): ?string
    {
        return $this->serviceRegistryName;
    }

    public function setServiceRegistryName(?string $serviceRegistryName): void
    {
        $this->serviceRegistryName = $serviceRegistryName;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function setAdditionalData(?array $additionalData, bool $addParametersInFileName = false): void
    {
        $this->additionalData = $additionalData;
        if ($addParametersInFileName) {
            $values = '_'.implode('_', array_values($additionalData));
            $this->fileName = str_replace('__', $values.'__', $this->fileName);

            $this->fileName = str_replace([',', ' '], ['_', '_'], $this->fileName);
        }
    }

    public function addToAdditionalData(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->additionalData[$key] = $value;
        }
    }
}
