<?php

declare(strict_types=1);

namespace Zorachka\Framework\Migrations\Cycle\Migrations;

use Psr\Container\ContainerInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Zorachka\Framework\Console\ConsoleConfig;
use Zorachka\Framework\Container\ServiceProvider;
use Zorachka\Framework\Directories\Directories;
use Zorachka\Framework\Directories\DirectoriesConfig;
use Zorachka\Framework\EventDispatcher\EventDispatcherConfig;
use Zorachka\Framework\EventDispatcher\NullableEventListener;
use Zorachka\Framework\Migrations\Cycle\Migrations\Console\Migration\CreateCommand;
use Zorachka\Framework\Migrations\Cycle\Migrations\Console\Migration\DownCommand;
use Zorachka\Framework\Migrations\Cycle\Migrations\Console\Migration\ListCommand;
use Zorachka\Framework\Migrations\Cycle\Migrations\Console\Migration\UpCommand;
use Zorachka\Framework\Migrations\Cycle\Migrations\Event\AfterMigrate;
use Zorachka\Framework\Migrations\Cycle\Migrations\Event\BeforeMigrate;
use Zorachka\Framework\Migrations\MigrationsConfig;

final class CycleMigrationsServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            MigrationConfig::class => static function (ContainerInterface $container) {
                /** @var MigrationsConfig $config */
                $config = $container->get(MigrationsConfig::class);
                /** @var Directories $directories */
                $directories = $container->get(Directories::class);

                return new MigrationConfig([
                    'directory' => $directories->get($config->directory()),
                    'table' => $config->table(),
                    'safe' => $config->isSafe(),
                ]);
            },
            Migrator::class => static function (ContainerInterface $container) {
                /** @var MigrationConfig $config */
                $migrationConfig = $container->get(MigrationConfig::class);
                /** @var DatabaseManager $dbal */
                $dbal = $container->get(DatabaseManager::class);

                $migrator = new Migrator(
                    $migrationConfig,
                    $dbal,
                    new FileRepository($migrationConfig)
                );

                if (!$migrator->isConfigured()) {
                    $migrator->configure();
                }

                return $migrator;
            },
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getExtensions(): array
    {
        return [
            EventDispatcherConfig::class => static function(EventDispatcherConfig $config) {
                return $config
                    ->withEventListener(BeforeMigrate::class, NullableEventListener::class)
                    ->withEventListener(AfterMigrate::class, NullableEventListener::class);
            },
            DirectoriesConfig::class => static function(DirectoriesConfig $config) {
                return $config->withDirectory('@migrations', '@root/migrations');
            },
            ConsoleConfig::class => function(ConsoleConfig $config) {
                return $config
                    ->withCommand(ListCommand::class)
                    ->withCommand(CreateCommand::class)
                    ->withCommand(UpCommand::class)
                    ->withCommand(DownCommand::class);
            },
        ];
    }
}
