<?php

namespace App\Maker;

use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Index;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;

class ExportEntityMaker extends AbstractMaker
{
    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:export-entity';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates export configuration for an entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Entity name (e.g., customer)')
            ->addArgument('sync', InputArgument::OPTIONAL, 'async or sync')
            ->setHelp('This command creates export configuration files for an entity');

        $inputConfig->setArgumentAsNonInteractive('name');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
        $sync = 'async' !== $input->getArgument('sync');
        $entityClassName = $this->findEntityClassByResourceName($name);

        if (null === $entityClassName) {
            $io->error(sprintf('Entity class for resource "%s" not found', $name));

            return;
        }

        $entityClass = new \ReflectionClass($entityClassName);
        if (!is_a($entityClass->newInstance(), ResourceInterface::class)) {
            $io->error(sprintf('Entity "%s" is not a Sylius resource', $name));

            return;
        }

        $gridData = $this->findGridForEntity($entityClassName);
        if (null === $gridData) {
            $io->error(sprintf('Grid for resource "%s" not found', $name));

            return;
        }

        // Ask for entity properties
        $properties = $this->askForEntityProperties($io);

        // Create or update HeadersConstants
        $this->createOrUpdateHeadersConstants($name, $properties, $io);

        // Create export route file
        $this->createExportRouteFile($name, $gridData['name'], $io, $sync);

        // Update main export routes file
        $this->updateMainExportRoutesFile($name, $io);

        // Create services configuration
        $this->createServicesConfiguration($name, $io, $sync);

        // Create ResourcePlugin class
        $this->createResourcePlugin($name, $entityClassName, $properties, $io, $generator);

        $io->success(sprintf('Export configuration created for entity "%s" with grid "%s"', $name, $gridData['name']));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // No additional dependencies needed
    }

    private function createExportRouteFile(string $name, string $gridName, ConsoleStyle $io, bool $sync): void
    {
        $routePath = sprintf('config/routes/admin/export/%s.yaml', $name);

        $exportController = sprintf('app.controller.export_data_%s::exportAction', $name);
        if (!$sync) {
            $exportController = 'App\Controller\Admin\CustomExportController::exportAction';
        }

        $routeContent = [
            sprintf('app_export_data_%s', $name) => [
                'path' => sprintf('/app.%s/{format}', $name),
                'methods' => ['GET'],
                'defaults' => [
                    'resource' => sprintf('app.%s', $name),
                    '_controller' => $exportController,
                    '_sylius' => [
                        'filterable' => true,
                        'grid' => $gridName,
                    ],
                ],
            ],
        ];

        $yamlContent = Yaml::dump($routeContent, 4, 4);

        // Ensure directory exists
        $this->filesystem->mkdir(dirname($routePath));

        // Write the file
        file_put_contents($routePath, $yamlContent);

        $io->text(sprintf('Created route file: %s', $routePath));
    }

    private function updateMainExportRoutesFile(string $name, ConsoleStyle $io): void
    {
        $mainRoutePath = 'config/routes/admin/export/_main.yaml';

        // Create main file if it doesn't exist
        if (!$this->filesystem->exists($mainRoutePath)) {
            $this->filesystem->mkdir(dirname($mainRoutePath));
            file_put_contents($mainRoutePath, '# Export routes'.PHP_EOL);
        }

        // Read existing content
        $existingContent = file_get_contents($mainRoutePath);

        // Add new route import
        $newImport = sprintf('%s_export:', $name).PHP_EOL;
        $newImport .= sprintf('    resource: "%s.yaml"', $name).PHP_EOL;
        $newImport .= sprintf('    prefix: /%s', $name).PHP_EOL.PHP_EOL;

        // Check if import already exists
        if (false === strpos($existingContent, sprintf('%s_export:', $name))) {
            file_put_contents($mainRoutePath, $existingContent.$newImport);
            $io->text(sprintf('Added import to main routes file: %s', $mainRoutePath));
        } else {
            $io->note(sprintf('Import for %s already exists in main routes file', $name));
        }
    }

    private function createServicesConfiguration(string $name, ConsoleStyle $io, bool $sync): void
    {
        $servicePath = sprintf('config/services/exporter/%s_exporter.yaml', $name);
        $className = $this->convertToClassName($name);
        $upperClassName = strtoupper($name);

        $csv = [
            'class' => 'FriendsOfSylius\\SyliusImportExportPlugin\\Exporter\\ResourceExporter',
            'arguments' => [
                '@sylius.exporter.csv_writer',
                sprintf('@app.exporter.pluginpool.%s', $name),
                new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperClassName)),
                '@sylius.exporters_transformer_pool',
            ],
            'tags' => [
                ['name' => 'sylius.exporter', 'type' => sprintf('app.%s', $name), 'format' => 'csv'],
            ],
        ];
        if (!$sync) {
            $csv = [
                'class' => 'App\\Exporter\\Resource\\AsyncResourceExporter',
                'arguments' => [
                    '@sylius.exporter.csv_writer',
                    sprintf('@app.exporter.pluginpool.%s', $name),
                    new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperClassName)),
                    '@sylius.exporters_transformer_pool',
                    sprintf('@app.repository.%s', $name),
                ],
                'tags' => [
                    ['name' => 'sylius.exporter', 'type' => sprintf('app.%s', $name), 'format' => 'csv'],
                ],
            ];
        }

        $serviceContent = [
            'services' => [
                '_defaults' => [
                    'autowire' => false,
                    'autoconfigure' => false,
                ],
                sprintf('app.controller.export_data_%s', $name) => [
                    'public' => true,
                    'class' => 'FriendsOfSylius\\SyliusImportExportPlugin\\Controller\\ExportDataController',
                    'arguments' => [
                        '$registry' => '@sylius.exporters_registry',
                        '$requestConfigurationFactory' => '@sylius.resource_controller.request_configuration_factory',
                        '$resourcesCollectionProvider' => '@sylius.resource_controller.resources_collection_provider',
                        '$repository' => sprintf('@app.repository.%s', $name),
                        '$resources' => '%sylius.resources%',
                    ],
                    'tags' => ['controller.service_arguments'],
                ],
                sprintf('app.exporter.orm.hydrator.%s', $name) => [
                    'class' => 'App\\Exporter\\ORM\\Hydrator\\EmptyHydrator',
                    'arguments' => [
                        '$repository' => sprintf('@app.repository.%s', $name),
                    ],
                ],
                sprintf('app.exporter.plugin.resource.%s', $name) => [
                    'class' => sprintf('App\\Exporter\\Plugin\\%sResourcePlugin', $className),
                    'arguments' => [
                        '$repository' => sprintf('@app.repository.%s', $name),
                        '$propertyAccessor' => '@property_accessor',
                        '$entityManager' => '@doctrine.orm.entity_manager',
                        '$entityHydrator' => sprintf('@app.exporter.orm.hydrator.%s', $name),
                    ],
                ],
                sprintf('app.exporter.pluginpool.%s', $name) => [
                    'class' => 'FriendsOfSylius\\SyliusImportExportPlugin\\Exporter\\Plugin\\PluginPool',
                    'arguments' => [
                        [sprintf('@app.exporter.plugin.resource.%s', $name)],
                        new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperClassName)),
                    ],
                ],
                sprintf('app.exporter.%s.csv', $name) => $csv,
            ],
        ];

        if ($sync) {
            $serviceContent['services'][sprintf('app.exporter.%s.xlsx', $name)] = [
                'class' => 'FriendsOfSylius\\SyliusImportExportPlugin\\Exporter\\ResourceExporter',
                'arguments' => [
                    '@sylius.exporter.spreadsheet_writer',
                    sprintf('@app.exporter.pluginpool.%s', $name),
                    new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperClassName)),
                    '@sylius.exporters_transformer_pool',
                ],
                'tags' => [
                    ['name' => 'sylius.exporter', 'type' => sprintf('app.%s', $name), 'format' => 'xlsx'],
                ],
            ];
        }

        if ($sync) {
            $serviceContent['services'][sprintf('app.listener.%s', $name)] = [
                'class' => 'FriendsOfSylius\\SyliusImportExportPlugin\\Listener\\ExportButtonGridListener',
                'arguments' => [
                    sprintf('app.%s', $name),
                    ['csv', 'xlsx'],
                ],
                'calls' => [
                    ['setRequest' => ['@request_stack']],
                ],
                'tags' => [
                    ['name' => 'kernel.event_listener', 'event' => sprintf('sylius.grid.%s', $name), 'method' => 'onSyliusGridAdmin'],
                ],
            ];
        }

        $yamlContent = Yaml::dump($serviceContent, 6, 4);

        // Ensure directory exists
        $this->filesystem->mkdir(dirname($servicePath));

        // Write the file
        file_put_contents($servicePath, $yamlContent);

        $io->text(sprintf('Created services file: %s', $servicePath));
    }

    private function convertToClassName(string $name): string
    {
        // Convert snake_case or kebab-case to PascalCase
        // Example: app_config -> AppConfig, customer -> Customer
        return str_replace([' ', '_', '-'], '', ucwords($name, ' _-'));
    }

    private function askForEntityProperties(ConsoleStyle $io): array
    {
        $io->section('Entity Properties Configuration');
        $io->text('Enter the entity properties you want to export (comma-separated):');
        $io->text('Example: id,name,email,createdAt');

        $propertiesInput = $io->ask('Properties', 'id');

        // Split by comma and clean up
        $properties = array_map('trim', explode(',', $propertiesInput));

        $io->listing($properties);

        return $properties;
    }

    private function createOrUpdateHeadersConstants(string $name, array $properties, ConsoleStyle $io): void
    {
        $constantsPath = 'src/Exporter/HeadersConstants.php';
        $upperName = strtoupper($name);

        // Create the constants file if it doesn't exist
        if (!$this->filesystem->exists($constantsPath)) {
            $this->createHeadersConstantsFile($constantsPath);
            $io->text('Created HeadersConstants file');
        }

        // Read existing content
        $content = file_get_contents($constantsPath);

        // Check if constant already exists
        if (false !== strpos($content, sprintf('const %s =', $upperName))) {
            $io->note(sprintf('Constant %s already exists in HeadersConstants', $upperName));

            return;
        }

        // Generate the constant array
        $constantArray = $this->generateConstantArray($properties);

        // Add the new constant before the last closing brace
        $newConstant = sprintf(
            "    public const %s = %s; \n }",
            $upperName,
            $constantArray
        );

        $content = rtrim($content, " \t\n\r\0\x0B}")."\n\n".$newConstant;

        file_put_contents($constantsPath, $content);

        $io->text(sprintf('Added constant %s to HeadersConstants', $upperName));
    }

    private function createHeadersConstantsFile(string $path): void
    {
        $this->filesystem->mkdir(dirname($path));

        $content = "<?php\n\nnamespace App\\Exporter;\n\nclass HeadersConstants\n{\n}\n";

        file_put_contents($path, $content);
    }

    private function generateConstantArray(array $properties): string
    {
        $formattedProperties = array_map(function ($property) {
            return sprintf("'%s'", $property);
        }, $properties);

        return "[\n        ".implode(",\n        ", $formattedProperties)."\n    ]";
    }

    private function createResourcePlugin(string $name, string $entityClassName, array $properties, ConsoleStyle $io, Generator $generator): void
    {
        $className = $this->convertToClassName($name);
        $pluginPath = sprintf('src/Exporter/Plugin/%sResourcePlugin.php', $className);

        // Check if file already exists
        if ($this->filesystem->exists($pluginPath)) {
            $io->note(sprintf('ResourcePlugin %s already exists', $className));

            return;
        }

        // Generate getter method calls for properties
        $getterCalls = $this->generateGetterCalls($properties);

        $generator->generateClass(
            sprintf('App\\Exporter\\Plugin\\%sResourcePlugin', $className),
            'src/Maker/Skeletons/Exporter/ResourcePlugin.tpl.php',
            [
                'class_name' => $className,
                'getter_calls' => $getterCalls,
                'namespace' => 'App\Exporter\Plugin',
                'entity_class' => $entityClassName,
            ]
        );

        $generator->writeChanges();

        $io->text(sprintf('Created ResourcePlugin: %s', $pluginPath));
    }

    private function generateGetterCalls(array $properties): string
    {
        $getterCalls = [];

        foreach ($properties as $property) {
            // Convert property name to camelCase for getter method
            // "exemplu_unu" or "exemplu unu" -> "getExempluUnu"
            $camelCaseProperty = $this->convertToCamelCase($property);
            $getterMethod = 'get'.ucfirst($camelCaseProperty);
            $getterCalls[] = sprintf(
                "        \$this->addDataForResource(\$resource, '%s', \$resource->%s());",
                $property,
                $getterMethod
            );
        }

        return implode("\n", $getterCalls);
    }

    private function convertToCamelCase(string $property): string
    {
        // Replace underscores and spaces with nothing, and capitalize the first letter of each word
        // "exemplu_unu" -> "exempluUnu", "exemplu unu" -> "exempluUnu"
        $words = preg_split('/[_\s]+/', $property);
        $camelCase = array_shift($words); // Keep first word as is

        foreach ($words as $word) {
            $camelCase .= ucfirst($word);
        }

        return $camelCase;
    }

    private function findEntityClassByResourceName(string $resourceName): ?string
    {
        $finder = new Finder();
        $finder->files()->in('src/Entity')->name('*.php');

        foreach ($finder as $file) {
            $content = $file->getContents();

            // Extract namespace and class name
            if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch)
                && preg_match('/class\s+(\w+)/', $content, $classMatch)) {
                $className = $namespaceMatch[1].'\\'.$classMatch[1];

                // Check if class exists and can be loaded
                if (class_exists($className)) {
                    try {
                        $reflection = new \ReflectionClass($className);
                        $attributes = $reflection->getAttributes(AsResource::class);

                        foreach ($attributes as $attribute) {
                            $asResource = $attribute->newInstance();
                            if ($asResource->toMetadata()->getName() === $resourceName) {
                                return $className;
                            }
                        }
                    } catch (\ReflectionException $e) {
                        // Skip if reflection fails
                        continue;
                    }
                }
            }
        }

        return null;
    }

    public function findGridForEntity(string $entityClassName): ?array
    {
        $reflection = new \ReflectionClass($entityClassName);

        $attributes = $reflection->getAttributes(AsResource::class);
        foreach ($attributes as $attribute) {
            /** @var AsResource $asResource */
            $asResource = $attribute->newInstance();
            $metadata = $asResource->toMetadata();
            foreach ($metadata->getOperations() as $operation) {
                if (is_a($operation, Index::class)) {
                    $gridClassName = $operation->getGrid();
                    $gridReflection = new \ReflectionClass($gridClassName);
                    $instance = $gridReflection->newInstance();
                    if (is_a($instance, GridInterface::class)) {
                        $name = $instance->getName();

                        return [
                            'name' => $name,
                            'grid_class_name' => $gridClassName,
                        ];
                    }
                }
            }
        }

        return null;
    }
}
