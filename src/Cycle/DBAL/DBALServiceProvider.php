<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\DBAL;

use Cycle\Database\Config;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Psr\Container\ContainerInterface;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\DatabaseConfig;
use Zorachka\Database\Driver;
use Zorachka\Database\Transaction;
use Zorachka\Directories\Directories;
use Zorachka\Directories\DirectoryAlias;

final class DBALServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            DatabaseManager::class => static function (ContainerInterface $container) {
                /** @var DatabaseConfig $config */
                $config = $container->get(DatabaseConfig::class);

                /** @var Directories $directories */
                $directories = $container->get(Directories::class);

                $mapper = [
                    Driver::PGSQL->value => 'postgres',
                ];

                $driver = $mapper[$config->driver()->value];

                return new DatabaseManager(
                    new Config\DatabaseConfig([
                        'databases' => [
                            'default' => [
                                'driver' => $driver,
                            ],
                        ],
                        'connections' => [
                            'runtime' => new Config\SQLiteDriverConfig(
                                connection: new Config\SQLite\FileConnectionConfig(
                                    database: $directories->get(DirectoryAlias::ROOT) . 'var/database.sqlite'
                                ),
                                queryCache: true,
                            ),
                            $driver => new Config\PostgresDriverConfig(
                                connection: new Config\Postgres\TcpConnectionConfig(
                                    database: $config->name(),
                                    host: $config->host(),
                                    port: $config->port(),
                                    user: $config->username(),
                                    password: $config->password(),
                                ),
                                schema: 'public',
                                queryCache: true,
                            ),
                        ],
                    ])
                );
            },
            Database::class => static function (ContainerInterface $container) {
                /** @var DatabaseManager $manager */
                $manager = $container->get(DatabaseManager::class);

                return $manager->database('default');
            },
            Transaction::class => static function (ContainerInterface $container) {
                /** @var Database $database */
                $database = $container->get(Database::class);

                return new CycleTransaction($database);
            },
        ];
    }

    public static function getExtensions(): array
    {
        return [];
    }
}
