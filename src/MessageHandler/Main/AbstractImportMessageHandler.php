<?php

namespace App\MessageHandler\Main;

use App\Importer\Main\DeleteCacheInterface;
use Doctrine\Persistence\ObjectManager;
use FriendsOfSylius\SyliusImportExportPlugin\Processor\ResourceProcessorInterface;

abstract class AbstractImportMessageHandler
{
    protected int $batchSize = 20;

    protected int $batchCount = 0;

    public function __construct(
        protected ObjectManager $objectManager,
        protected ResourceProcessorInterface $resourceProcessor,
    ) {
    }

    public function import(array $data): void
    {
        try {
            foreach ($data as $i => $row) {
                if (!$row) {
                    continue;
                }

                if ($this->importData((int) $i, $row)) {
                    break;
                }
            }
        } catch (\Throwable $throwable) {
            if ($this->resourceProcessor instanceof DeleteCacheInterface) {
                $this->resourceProcessor->deleteCache();
            }

            throw $throwable;
        }

        if ($this->batchCount) {
            $this->objectManager->flush();
        }

        $this->objectManager->clear();

        if ($this->resourceProcessor instanceof DeleteCacheInterface) {
            $this->resourceProcessor->deleteCache();
        }
    }

    public function importData(int $i, array $row): bool
    {
        try {
            $this->resourceProcessor->process($row);

            ++$this->batchCount;
            if ($this->batchSize && $this->batchCount === $this->batchSize) {
                $this->objectManager->flush();
                $this->batchCount = 0;
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        return false;
    }
}
