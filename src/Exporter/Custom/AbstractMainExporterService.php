<?php

namespace App\Exporter\Custom;

use App\Entity\Log\ExportFileLog;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractMainExporterService implements ExporterServiceInterface
{
    protected readonly EntityManagerInterface $entityManager; // @phpstan-ignore-line
    protected ?ExportFileLog $exportFileLog = null;
    protected string $name = '';
    protected array $fileNames = [];

    public function __construct()
    {
    }

    public function setExportFileLog(ExportFileLog $exportFileLog): void
    {
        $this->exportFileLog = $exportFileLog;
    }

    public function setStatus(string $status): void
    {
        $exportFile = $this->entityManager->getRepository(ExportFileLog::class)
            ->findOneBy(['externalId' => $this->exportFileLog->getExternalId()]);

        $exportFile->setStatus($status);

        $this->entityManager->persist($exportFile);
        $this->entityManager->flush();
    }

    protected function writeToCsv(array $data, ?string $fileName = null): void
    {
        if (!$fileName) {
            $fileName = $this->exportFileLog->getFileName();
        }

        $folderPath = sys_get_temp_dir().'/export_files/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $fp = fopen($folderPath.$fileName, 'a');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function zipFiles(): void
    {
        if (!$this->fileNames) {
            throw new \Exception('No files were found');
        }

        $folderPath = sys_get_temp_dir().'/export_files/';

        $zip = new \ZipArchive();
        $zipFileName = $folderPath.$this->exportFileLog->getFileName();

        if (true === $zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            foreach ($this->fileNames as $file) {
                $fullFilePath = $folderPath.$file;
                if (file_exists($fullFilePath)) {
                    // Add the file to the ZIP archive
                    $zip->addFile($fullFilePath, basename($fullFilePath));
                } else {
                    echo "File does not exist: $file\n";
                }
            }
            // Close the ZIP archive
            $zip->close();
            echo "ZIP archive created successfully: $zipFileName\n";
        } else {
            echo "Failed to create ZIP archive.\n";
        }

        $this->deleteFiles();
    }

    protected function deleteFiles(): void
    {
        $folderPath = sys_get_temp_dir().'/export_files/';

        foreach ($this->fileNames as $file) {
            $fullFilePath = $folderPath.$file;
            unlink($fullFilePath);
        }
    }

    protected function getNextElement(array $array, int $currentElement): ?int
    {
        $index = array_search($currentElement, $array);
        if (false === $index || $index === count($array) - 1) {
            return null;
        }

        return (int) $array[$index + 1];
    }

    protected function getKeyFromAdditionalData(string $key): mixed
    {
        if (!isset($this->exportFileLog->getAdditionalData()[$key])) {
            return null;
        }

        return $this->exportFileLog->getAdditionalData()[$key];
    }

    protected function getActorType(string $actorTypeKey): string
    {
        if (!isset($this->exportFileLog->getAdditionalData()[$actorTypeKey])) {
            throw new \Exception('Actor type is mandatory');
        }

        return $this->exportFileLog->getAdditionalData()[$actorTypeKey];
    }

    protected function filterSelectedValues(string $fieldName, array $values): array
    {
        $additionalData = $this->exportFileLog->getAdditionalData();
        if (!isset($additionalData[$fieldName]) || !$additionalData[$fieldName]) {
            return $values;
        }

        $response = [];
        $fields = explode(',', $additionalData[$fieldName]);
        foreach ($fields as $field) {
            if (array_key_exists($field, $values)) {
                $response[$field] = $values[$field];
            }
        }

        return $response;
    }
}
