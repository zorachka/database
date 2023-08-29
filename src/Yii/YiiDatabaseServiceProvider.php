<?php

declare(strict_types=1);

namespace Zorachka\Database\Yii;

use Psr\Container\ContainerInterface;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Pgsql\Connection;
use Yiisoft\Db\Pgsql\Driver;
use Yiisoft\Db\Pgsql\Dsn;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\DatabaseConfig;
use Zorachka\Database\Repository\EntityRepository;
use Zorachka\Database\Transaction;

final class YiiDatabaseServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            ConnectionInterface::class => static function (ContainerInterface $container) {
                /** @var DatabaseConfig $config */
                $config = $container->get(DatabaseConfig::class);

                // Dsn.
                $dsn = (new Dsn($config->driver()->value, $config->host(), $config->name(), (string)$config->port()))->asString();

                // PSR-16 cache implementation.
                $cache = new ArrayCache();

                // Schema cache.
                $schemaCache = new SchemaCache($cache);

                // PDO driver.
                $pdoDriver = new Driver($dsn, $config->username() ?? '', $config->password() ?? '');

                // Connection.
                return new Connection($pdoDriver, $schemaCache);
            },
            Transaction::class => static function (ContainerInterface $container) {
                /** @var ConnectionInterface $connection */
                $connection = $container->get(ConnectionInterface::class);

                return new TransactionUsingYii($connection);
            },
            EntityRepository::class => static function (ContainerInterface $container) {
                /** @var ConnectionInterface $connection */
                $connection = $container->get(ConnectionInterface::class);

                return new EntityRepositoryUsingYii($connection);
            },
        ];
    }

    public static function getExtensions(): array
    {
        return [];
    }
}
