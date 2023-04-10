<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Migrations;

use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Psr\Container\ContainerInterface;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\Cycle\Migrations\Event\AfterMigrate;
use Zorachka\Database\Cycle\Migrations\Event\BeforeMigrate;
use Zorachka\Database\MigrationsConfig;
use Zorachka\Directories\Directories;
use Zorachka\EventDispatcher\EventDispatcherConfig;
use Zorachka\EventDispatcher\NullableEventListener;

final class MigrationsServiceProvider implements ServiceProvider
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
                /** @var MigrationConfig $migrationConfig */
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

    public static function getExtensions(): array
    {
        return [
            EventDispatcherConfig::class => static function (EventDispatcherConfig $config) {
                return $config
                    ->withEventListener(BeforeMigrate::class, NullableEventListener::class)
                    ->withEventListener(AfterMigrate::class, NullableEventListener::class);
            },
        ];
    }
}
