<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database\Cycle\Migrations;

use Psr\Container\ContainerInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\FileRepository;
use Zorachka\Framework\Container\ServiceProvider;
use Zorachka\Framework\Database\Cycle\Migrations\Event\AfterMigrate;
use Zorachka\Framework\Database\Cycle\Migrations\Event\BeforeMigrate;
use Zorachka\Framework\Database\MigrationsConfig;
use Zorachka\Framework\Directories\Directories;
use Zorachka\Framework\EventDispatcher\EventDispatcherConfig;
use Zorachka\Framework\EventDispatcher\NullableEventListener;

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
                    'safe' => $config->safe(),
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
        ];
    }
}
