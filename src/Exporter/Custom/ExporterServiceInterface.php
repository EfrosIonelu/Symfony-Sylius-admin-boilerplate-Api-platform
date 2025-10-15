<?php

namespace App\Exporter\Custom;

use App\Entity\Log\ExportFileLog;

interface ExporterServiceInterface
{
    public function setExportFileLog(ExportFileLog $exportFileLog): void;

    public function export(): bool;

    public function setStatus(string $status): void;

    public function clear(): void;

    public function getName(): string;
}
