<?php

declare(strict_types=1);

namespace Zorachka\Database\Doctrine\Migrations;

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\Migrations\Tools\Console\Command;
use Psr\Container\ContainerInterface;
use Zorachka\Console\ConsoleConfig;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\DatabaseConfig;
use Zorachka\Database\Driver;
use Zorachka\Database\MigrationsConfig;
use Zorachka\Directories\Directories;

final class MigrationsServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            DependencyFactory::class => static function (
                ContainerInterface $container
            ) {
                /** @var DatabaseConfig $databaseConfig */
                $databaseConfig = $container->get(DatabaseConfig::class);

                $mapper = [
                    Driver::PGSQL->value => 'pdo_pgsql',
                ];

                $dbParams = [
                    'dbname' => $databaseConfig->name(),
                    'user' => $databaseConfig->username(),
                    'password' => $databaseConfig->password(),
                    'host' => $databaseConfig->host(),
                    'driver' => $mapper[$databaseConfig->driver()->value],
                ];

                /** @phpstan-ignore-next-line */
                $connection = DriverManager::getConnection($dbParams);

                $configuration = new Configuration();

                /** @var MigrationsConfig $migrationsConfig */
                $migrationsConfig = $container->get(MigrationsConfig::class);
                /** @var Directories $directories */
                $directories = $container->get(Directories::class);

                $configuration->addMigrationsDirectory(
                    $migrationsConfig->namespace(),
                    $directories->get($migrationsConfig->directory())
                );
                $configuration->setAllOrNothing(true);
                $configuration->setCheckDatabasePlatform(false);

                $configuration->setCustomTemplate($migrationsConfig->customTemplatePath());

                $storageConfiguration = new TableMetadataStorageConfiguration();
                $storageConfiguration->setTableName(
                    $migrationsConfig->table()
                );

                $configuration->setMetadataStorageConfiguration(
                    $storageConfiguration
                );

                $dependencyFactory = DependencyFactory::fromConnection(
                    new ExistingConfiguration($configuration),
                    new ExistingConnection($connection)
                );

                $dependencyFactory->setService(
                    SchemaProvider::class,
                    new AggregateSchemaProvider($migrationsConfig->aggregateClasses())
                );

                return $dependencyFactory;
            },
            Command\ExecuteCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\ExecuteCommand($factory);
            },
            Command\MigrateCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\MigrateCommand($factory);
            },
            Command\LatestCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\LatestCommand($factory);
            },
            Command\ListCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\ListCommand($factory);
            },
            Command\StatusCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\StatusCommand($factory);
            },
            Command\UpToDateCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\UpToDateCommand($factory);
            },
            Command\DiffCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\DiffCommand($factory);
            },
            Command\GenerateCommand::class => static function (ContainerInterface $container) {
                /** @var DependencyFactory $factory */
                $factory = $container->get(DependencyFactory::class);

                return new Command\GenerateCommand($factory);
            },
        ];
    }

    public static function getExtensions(): array
    {
        return [
            ConsoleConfig::class => static function (ConsoleConfig $config) {
                return $config
                    ->withCommand(Command\CurrentCommand::class)
                    ->withCommand(Command\DiffCommand::class)
                    ->withCommand(Command\DumpSchemaCommand::class)
                    ->withCommand(Command\ExecuteCommand::class)
                    ->withCommand(Command\GenerateCommand::class)
                    ->withCommand(Command\LatestCommand::class)
                    ->withCommand(Command\ListCommand::class)
                    ->withCommand(Command\MigrateCommand::class)
                    ->withCommand(Command\RollupCommand::class)
                    ->withCommand(Command\StatusCommand::class)
                    ->withCommand(Command\SyncMetadataCommand::class)
                    ->withCommand(Command\UpToDateCommand::class)
                    ->withCommand(Command\VersionCommand::class);
            },
        ];
    }
}
