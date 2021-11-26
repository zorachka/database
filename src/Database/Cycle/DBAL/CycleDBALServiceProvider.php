<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database\Cycle\DBAL;

use Psr\Container\ContainerInterface;
use Cycle\Database\Config\DatabaseConfig as CycleDatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\Postgres\PostgresDriver;
use Zorachka\Framework\Container\ServiceProvider;
use Zorachka\Framework\Database\DatabaseConfig;
use Zorachka\Framework\Database\Transaction;

final class CycleDBALServiceProvider implements ServiceProvider
{
    /**
     * @inheritDoc
     */
    public static function getDefinitions(): array
    {
        return [
            Database::class => static function (ContainerInterface $container) {
                /** @var DatabaseManager $database */
                $manager = $container->get(DatabaseManager::class);

                /** @var Database $database */
                return $manager->database('default');
            },
            Transaction::class => static function (ContainerInterface $container) {
                /** @var Database $database */
                $database = $container->get(Database::class);

                return new CycleTransaction($database);
            },
            DatabaseManager::class => static function (ContainerInterface $container) {
                /** @var DatabaseConfig $config */
                $config = $container->get(DatabaseConfig::class);

                return new DatabaseManager(new CycleDatabaseConfig([
                    'databases'   => [
                        'default' => ['connection' => 'postgres'],
                    ],
                    'connections' => [
                        'postgres' => [
                            'driver' => PostgresDriver::class,
                            'options' => [
                                'connection' => 'pgsql:host=' . $config->host() . ';dbname=' . $config->name(),
                                'username' => $config->username(),
                                'password' => $config->password(),
                            ]
                        ],
                    ],
                ]));
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getExtensions(): array
    {
        return [];
    }
}
