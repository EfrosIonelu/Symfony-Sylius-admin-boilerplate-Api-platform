<?php

namespace App\MessageHandler\ExportFile;

use App\Entity\Log\ExportFileLog;
use App\Exporter\Custom\AsyncResourceManagerExporter;
use App\Exporter\Custom\ExporterFactory;
use App\Exporter\Custom\ExporterServiceInterface;
use App\Exporter\Resource\Interfaces\ResourceExporterInterface;
use App\Message\ExportFile\ExportFileMessage;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExportFileMessageHandler
{
    private ?ExportFileLog $exportFileLog;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ServiceRegistryInterface $serviceRegistry,
        private readonly ExporterFactory $exporterFactory,
    ) {
    }

    public function __invoke(ExportFileMessage $exportFileMessage): void
    {
        sleep(1);

        $this->exportFileLog = $this->entityManager->getRepository(ExportFileLog::class)
            ->findOneBy(['externalId' => $exportFileMessage->getExternalId()]);

        if (!$this->exportFileLog) {
            echo "\n [error] ExportFileLog not found\n";

            return;
        }

        if (!$service = $this->getService()) {
            echo "\n [error] Service not found \n";

            return;
        }

        $service->setExportFileLog($this->exportFileLog);
        $status = $service->export();
        $service->setStatus($status ? ExportFileLog::SUCCESS : ExportFileLog::ERROR);
        $service->clear();
    }

    private function getService(): ?ExporterServiceInterface
    {
        try {
            $exporterService = $this->exporterFactory->getByName($this->exportFileLog->getServiceRegistryName());

            if ($exporterService instanceof AsyncResourceManagerExporter) {
                $service = $this->serviceRegistry->get($this->exportFileLog->getServiceRegistryName());
                if (!$service instanceof ResourceExporterInterface) {
                    echo "\n [error] Service is not instance of ResourceExporterInterface view your configuration inside ~/config/services/exporter \n";

                    return null;
                }

                $exporterService->setResourceExporter($service);
            }

            return $exporterService;
        } catch (\Throwable $exception) {
            echo "\n".$exception->getMessage()."\n";

            return null;
        }
    }
}
