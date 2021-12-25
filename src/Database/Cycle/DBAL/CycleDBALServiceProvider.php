<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database\Cycle\DBAL;

use Psr\Container\ContainerInterface;
use Cycle\Database\Config\Postgres\TcpConnectionConfig;
use Cycle\Database\Config\PostgresDriverConfig;
use Cycle\Database\Config\DatabaseConfig as CycleDatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
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
            DatabaseManager::class => static function (ContainerInterface $container) {
                /** @var DatabaseConfig $config */
                $config = $container->get(DatabaseConfig::class);

                return new DatabaseManager(new CycleDatabaseConfig([
                    'databases'   => [
                        'default' => ['connection' => 'postgres'],
                    ],
                    'connections' => [
                        'postgres' => new PostgresDriverConfig(
                            connection: new TcpConnectionConfig(
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
                ]));
            },
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
