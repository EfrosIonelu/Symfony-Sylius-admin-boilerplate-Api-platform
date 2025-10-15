<?php

namespace App\Importer\Main;

use FriendsOfSylius\SyliusImportExportPlugin\Importer\ImporterInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Importer\ImporterResultInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Importer\ImportResultLoggerInterface;
use Port\Reader\ReaderFactory;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractImporter implements ImporterInterface
{
    abstract public function dispatch(array $data): void;

    public function __construct(
        protected readonly ReaderFactory $readerFactory,
        protected ImportResultLoggerInterface $importerResult,
        protected readonly MessageBusInterface $messageBus,
    ) {
    }

    public function import(string $fileName): ImporterResultInterface
    {
        $this->importerResult->start();

        $reader = $this->readerFactory->getReader(new \SplFileObject($fileName));

        $data = [];
        foreach ($reader as $row) {
            $data[] = $row;
        }

        $this->dispatch($data);

        $this->importerResult->stop();

        return $this->importerResult;
    }
}
