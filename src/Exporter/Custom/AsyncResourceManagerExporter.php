<?php

namespace App\Exporter\Custom;

use App\Exporter\Resource\Interfaces\ClearableInterface;
use App\Exporter\Resource\Interfaces\GetLastIdsInterface;
use App\Exporter\Resource\Interfaces\ResourceExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\PluginPoolInterface;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Component\Grid\Filtering\FiltersApplicatorInterface;
use Sylius\Component\Grid\Parameters;

final class AsyncResourceManagerExporter extends AbstractMainExporterService
{
    protected string $name = ExporterFactory::DEFAULT_RESOURCE_EXPORTER;
    protected const CRITERIA_IDENTIFIER = 'criteria';
    protected const GRID_IDENTIFIER = 'grid';

    public const BATCH_SIZE = 500;
    protected ?ResourceExporterInterface $resourceExporter = null;
    protected ?PluginPoolInterface $pluginPool = null;
    protected ?GetLastIdsInterface $repository = null;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly ExporterGridProvider $exporterGridProvider,
        protected readonly FiltersApplicatorInterface $filtersApplicator,
    ) {
        parent::__construct();
    }

    public function setResourceExporter(ResourceExporterInterface $resourceExporter): void
    {
        $this->resourceExporter = $resourceExporter;
        $this->entityManager->getConnection()->getConfiguration()->getSQLLogger();
    }

    public function export(): bool
    {
        try {
            $repository = $this->resourceExporter->getRepository();
            if (!$repository instanceof GetLastIdsInterface) {
                echo sprintf("\n [error] %s is not instance of GetLastIdsInterface \n", $repository::class);

                return false;
            }

            $this->repository = $repository;

            $this->writeToCsv([$this->resourceExporter->getResourceKeys()]);

            $this->pluginPool = $this->resourceExporter->getPluginPool();

            $this->batch(0);

            return true;
        } catch (\Throwable $exception) {
            echo "\n".$exception->getMessage()."\n";

            return false;
        }
    }

    public function clear(): void
    {
        $this->resourceExporter = null;
        $this->exportFileLog = null;
        $this->pluginPool = null;
        $this->repository = null;
    }

    public function batch($lastItem, ?string $fileName = null, int $microSleep = 1): void
    {
        while (true) {
            $items = $this->getItems($lastItem);
            if (0 === count($items)) {
                break;
            }

            $this->processItems($items, $fileName);
            $lastItem = end($items);

            $this->entityManager->clear();
            unset($items);

            usleep($microSleep);
        }
    }

    protected function getItems(int $lastItem): array
    {
        $criteria = $this->getKeyFromAdditionalData(self::CRITERIA_IDENTIFIER);
        $grid = $this->getKeyFromAdditionalData(self::GRID_IDENTIFIER);

        $queryBuilder = $this->repository->getLastIds($lastItem, self::BATCH_SIZE);

        if ($criteria && $grid) {
            $grid = $this->exporterGridProvider->get($grid);
            $dataSource = new DataSource($queryBuilder, false, false);
            $parameters = new Parameters(['criteria' => $criteria]);
            $this->filtersApplicator->apply($dataSource, $grid, $parameters);
        }

        return $queryBuilder->getQuery()->getSingleColumnResult();
    }

    protected function processItems(array $items, ?string $fileName = null): void
    {
        $this->pluginPool->initPlugins($items);

        $response = [];
        foreach ($items as $item) {
            $response[] = $this->pluginPool->getDataForId((string) $item);
        }

        $this->writeToCsv($response, $fileName);

        foreach ($this->pluginPool->getPlugins() as $plugin) {
            if ($plugin instanceof ClearableInterface) {
                $plugin->clearCacheData();
            }
        }
    }
}
