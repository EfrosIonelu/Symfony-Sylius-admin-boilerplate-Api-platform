<?php

namespace App\Maker;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
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

class ImportEntityMaker extends AbstractMaker
{
    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:import-entity';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates import configuration for an entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Entity name (e.g., customer)')
            ->setHelp('This command creates import configuration files for an entity');

        $inputConfig->setArgumentAsNonInteractive('name');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
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

        // Create messenger configuration
        $this->createMessengerConfiguration($name, 'import', $io);

        // Create import route file
        $this->createImportRouteFile($name, $io);

        // Update main import routes file
        $this->updateMainImportRoutesFile($name, $io);

        // Create importer services configuration
        $this->createImporterServicesConfiguration($name, $io);

        // Update message handler configuration
        $this->updateMessageHandlerConfiguration($name, $io);

        // Generate Importer classes
        $this->generateImporterClasses($name, $io, $generator);

        $io->success(sprintf('Import configuration created for entity "%s"', $name));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // No additional dependencies needed
    }

    private function createMessengerConfiguration(string $name, string $type, ConsoleStyle $io): void
    {
        $className = $this->convertToClassName($name);
        $messengerPath = sprintf('config/services/messenger/%s_%s_messenger.yaml', $name, $type);

        $messengerContent = [
            'framework' => [
                'messenger' => [
                    'transports' => [
                        sprintf('%s_%s_message', $name, $type) => [
                            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                            'retry_strategy' => [
                                'max_retries' => 0,
                                'delay' => 1000,
                            ],
                            'options' => [
                                'exchange' => [
                                    'name' => sprintf('%s_message_%%env(APP_ENV)%%', $name),
                                ],
                                'queues' => [
                                    sprintf('%s_message_%%env(APP_ENV)%%', $name) => null,
                                ],
                            ],
                        ],
                    ],
                    'routing' => [
                        sprintf('App\\Message\\%s\\%s%sMessage', $className, $className, ucfirst($type)) => 'sync',
                    ],
                ],
            ],
        ];

        $yamlContent = Yaml::dump($messengerContent, 6, 4);

        // Ensure directory exists
        $this->filesystem->mkdir(dirname($messengerPath));

        // Write the file
        file_put_contents($messengerPath, $yamlContent);

        $io->text(sprintf('Created messenger configuration: %s', $messengerPath));
    }

    private function convertToClassName(string $name): string
    {
        // Convert snake_case or kebab-case to PascalCase
        // Example: app_config -> AppConfig, customer -> Customer
        return str_replace([' ', '_', '-'], '', ucwords($name, ' _-'));
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

    private function createImportRouteFile(string $name, ConsoleStyle $io): void
    {
        $routePath = sprintf('config/routes/admin/import/%s.yaml', $name);
        $upperName = strtoupper($name);

        $routeContent = [
            sprintf('app_backend_%s_import', $name) => [
                'path' => '/{resource}',
                'methods' => ['GET'],
                'controller' => 'Symfony\Bundle\FrameworkBundle\Controller\TemplateController',
                'defaults' => [
                    'template' => 'admin/grid/importer/create.html.twig',
                    'fields' => new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperName)),
                    'index_route' => sprintf('app_admin_%s_index', $name),
                ],
            ],
        ];

        $yamlContent = Yaml::dump($routeContent, 4, 4);

        // Ensure directory exists
        $this->filesystem->mkdir(dirname($routePath));

        // Write the file
        file_put_contents($routePath, $yamlContent);

        $io->text(sprintf('Created import route file: %s', $routePath));
    }

    private function updateMainImportRoutesFile(string $name, ConsoleStyle $io): void
    {
        $mainRoutePath = 'config/routes/admin/import/_main.yaml';

        // Create main file if it doesn't exist
        if (!$this->filesystem->exists($mainRoutePath)) {
            $this->filesystem->mkdir(dirname($mainRoutePath));
            file_put_contents($mainRoutePath, '# Import routes'.PHP_EOL);
        }

        // Read existing content
        $existingContent = file_get_contents($mainRoutePath);

        // Add new route import
        $newImport = sprintf('app_import_%s:', $name).PHP_EOL;
        $newImport .= sprintf('    resource: "%s.yaml"', $name).PHP_EOL;
        $newImport .= sprintf('    prefix: /%s', $name).PHP_EOL.PHP_EOL;

        // Check if import already exists
        if (false === strpos($existingContent, sprintf('app_import_%s:', $name))) {
            file_put_contents($mainRoutePath, $existingContent.$newImport);
            $io->text(sprintf('Added import to main import routes file: %s', $mainRoutePath));
        } else {
            $io->note(sprintf('Import for %s already exists in main import routes file', $name));
        }
    }

    private function createImporterServicesConfiguration(string $name, ConsoleStyle $io): void
    {
        $className = $this->convertToClassName($name);
        $upperName = strtoupper($name);
        $servicePath = sprintf('config/services/importer/%s_importer.yaml', $name);

        $serviceContent = [
            'services' => [
                '_defaults' => [
                    'autowire' => true,
                    'autoconfigure' => true,
                ],
                sprintf('App\\Importer\\%s\\%sProcessor', $className, $className) => [
                    'arguments' => [
                        '$resourceFactory' => sprintf('@app.factory.%s', $name),
                        '$resourceRepository' => sprintf('@app.repository.%s', $name),
                        '$entityManager' => '@doctrine.orm.entity_manager',
                        '$metadataValidator' => '@sylius.importer.metadata_validator',
                        '$headerKeys' => new TaggedValue('php/const', sprintf('App\\Exporter\\HeadersConstants::%s', $upperName)),
                    ],
                ],
                sprintf('app.importer.%s.csv', $name) => [
                    'class' => sprintf('App\\Importer\\%s\\%sImporter', $className, $className),
                    'arguments' => [
                        '$readerFactory' => '@sylius.factory.csv_reader',
                        '$importerResult' => '@sylius.importer.result',
                    ],
                    'tags' => [
                        ['name' => 'sylius.importer', 'type' => sprintf('app.%s', $name), 'format' => 'csv'],
                    ],
                ],
                sprintf('app.importer.%s.xlsx', $name) => [
                    'class' => sprintf('App\\Importer\\%s\\%sImporter', $className, $className),
                    'arguments' => [
                        '$readerFactory' => '@sylius.factory.spreadsheet_reader',
                        '$importerResult' => '@sylius.importer.result',
                    ],
                    'tags' => [
                        ['name' => 'sylius.importer', 'type' => sprintf('app.%s', $name), 'format' => 'xlsx'],
                    ],
                ],
                sprintf('app.listener.%s_importer', $name) => [
                    'class' => 'App\\EventListener\\Sylius\\ImportButtonGridListener',
                    'arguments' => [
                        sprintf('app.%s', $name),
                    ],
                    'tags' => [
                        [
                            'name' => 'kernel.event_listener',
                            'event' => sprintf('sylius.grid.app_%s', $name),
                            'method' => 'onSyliusGridAdmin',
                        ],
                    ],
                ],
            ],
        ];

        $yamlContent = Yaml::dump($serviceContent, 6, 4);

        // Ensure directory exists
        $this->filesystem->mkdir(dirname($servicePath));

        // Write the file
        file_put_contents($servicePath, $yamlContent);

        $io->text(sprintf('Created importer services configuration: %s', $servicePath));
    }

    private function updateMessageHandlerConfiguration(string $name, ConsoleStyle $io): void
    {
        $className = $this->convertToClassName($name);
        $messageHandlerPath = 'config/services/message_handler.yaml';

        // Check if file exists
        if (!$this->filesystem->exists($messageHandlerPath)) {
            // Create file with default structure
            $defaultContent = [
                'services' => [
                    '_defaults' => [
                        'autowire' => true,
                        'autoconfigure' => true,
                    ],
                ],
            ];

            $this->filesystem->mkdir(dirname($messageHandlerPath));
            file_put_contents($messageHandlerPath, Yaml::dump($defaultContent, 4, 4));
            $io->text('Created message_handler.yaml file');
        }

        // Read existing content
        $existingContent = file_get_contents($messageHandlerPath);
        $config = Yaml::parse($existingContent);

        // Ensure services section exists
        if (!isset($config['services'])) {
            $config['services'] = [];
        }

        // Add new message handler
        $messageHandlerKey = sprintf('App\\MessageHandler\\%s\\%sImportMessageHandler', $className, $className);

        // Check if handler already exists
        if (isset($config['services'][$messageHandlerKey])) {
            $io->note(sprintf('Message handler for %s already exists', $name));

            return;
        }

        $config['services'][$messageHandlerKey] = [
            'arguments' => [
                '$objectManager' => '@doctrine.orm.entity_manager',
                '$resourceProcessor' => sprintf('@App\\Importer\\%s\\%sProcessor', $className, $className),
            ],
        ];

        // Write updated content
        $yamlContent = Yaml::dump($config, 6, 4);
        file_put_contents($messageHandlerPath, $yamlContent);

        $io->text(sprintf('Added message handler for %s to message_handler.yaml', $name));
    }

    private function generateImporterClasses(string $name, ConsoleStyle $io, Generator $generator): void
    {
        $className = $this->convertToClassName($name);

        // Generate Importer class
        $this->generateImporterClass($name, $className, $io, $generator);

        // Generate Processor class (if template exists)
        $this->generateProcessorClass($name, $className, $io, $generator);

        // Generate Message class
        $this->generateMessageClass($name, $className, $io, $generator);

        // Generate MessageHandler class
        $this->generateMessageHandlerClass($name, $className, $io, $generator);

        $generator->writeChanges();
    }

    private function generateImporterClass(string $name, string $className, ConsoleStyle $io, Generator $generator): void
    {
        $importerPath = sprintf('src/Importer/%s/%sImporter.php', $className, $className);

        // Check if file already exists
        if ($this->filesystem->exists($importerPath)) {
            $io->note(sprintf('Importer class %sImporter already exists', $className));

            return;
        }

        $generator->generateClass(
            sprintf('App\\Importer\\%s\\%sImporter', $className, $className),
            'src/Maker/Skeletons/Inporter/EntityImporter.tpl.php',
            [
                'namespace' => sprintf('App\\Importer\\%s', $className),
                'className' => sprintf('%sImporter', $className),
                'entityNamePasscalCase' => $className,
                'entityImportMessageClassName' => sprintf('%sImportMessage', $className),
            ]
        );

        $io->text(sprintf('Generated Importer class: %s', $importerPath));
    }

    private function generateProcessorClass(string $name, string $className, ConsoleStyle $io, Generator $generator): void
    {
        // Check if processor template exists
        $processorTemplate = 'src/Maker/Skeletons/Inporter/EntityProcessor.tpl.php';
        if (!$this->filesystem->exists($processorTemplate)) {
            $io->note('Processor template not found, skipping processor generation');

            return;
        }

        $processorPath = sprintf('src/Importer/%s/%sProcessor.php', $className, $className);

        // Check if file already exists
        if ($this->filesystem->exists($processorPath)) {
            $io->note(sprintf('Processor class %sProcessor already exists', $className));

            return;
        }

        $generator->generateClass(
            sprintf('App\\Importer\\%s\\%sProcessor', $className, $className),
            $processorTemplate,
            [
                'namespace' => sprintf('App\\Importer\\%s', $className),
                'className' => sprintf('%sProcessor', $className),
                'entityNamePasscalCase' => $className,
            ]
        );

        $io->text(sprintf('Generated Processor class: %s', $processorPath));
    }

    private function generateMessageClass(string $name, string $className, ConsoleStyle $io, Generator $generator): void
    {
        // Check if message template exists
        $messageTemplate = 'src/Maker/Skeletons/Inporter/EntityImportMessage.tpl.php';
        if (!$this->filesystem->exists($messageTemplate)) {
            $io->note('Message template not found, skipping message generation');

            return;
        }

        $messagePath = sprintf('src/Message/%s/%sImportMessage.php', $className, $className);

        // Check if file already exists
        if ($this->filesystem->exists($messagePath)) {
            $io->note(sprintf('Message class %sImportMessage already exists', $className));

            return;
        }

        $generator->generateClass(
            sprintf('App\\Message\\%s\\%sImportMessage', $className, $className),
            $messageTemplate,
            [
                'namespace' => sprintf('App\\Message\\%s', $className),
                'className' => sprintf('%sImportMessage', $className),
                'entityNamePasscalCase' => $className,
            ]
        );

        $io->text(sprintf('Generated Message class: %s', $messagePath));
    }

    private function generateMessageHandlerClass(string $name, string $className, ConsoleStyle $io, Generator $generator): void
    {
        // Check if message handler template exists
        $handlerTemplate = 'src/Maker/Skeletons/Inporter/EntityImportMessageHandler.tpl.php';
        if (!$this->filesystem->exists($handlerTemplate)) {
            $io->note('MessageHandler template not found, skipping handler generation');

            return;
        }

        $handlerPath = sprintf('src/MessageHandler/%s/%sImportMessageHandler.php', $className, $className);

        // Check if file already exists
        if ($this->filesystem->exists($handlerPath)) {
            $io->note(sprintf('MessageHandler class %sImportMessageHandler already exists', $className));

            return;
        }

        $generator->generateClass(
            sprintf('App\\MessageHandler\\%s\\%sImportMessageHandler', $className, $className),
            $handlerTemplate,
            [
                'namespace' => sprintf('App\\MessageHandler\\%s', $className),
                'className' => sprintf('%sImportMessageHandler', $className),
                'entityNamePasscalCase' => $className,
                'messageClassName' => sprintf('%sImportMessage', $className),
            ]
        );

        $io->text(sprintf('Generated MessageHandler class: %s', $handlerPath));
    }
}
