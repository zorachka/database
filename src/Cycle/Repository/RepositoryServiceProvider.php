<?php

declare(strict_types=1);

namespace Zorachka\Database\Cycle\Repository;

use Cycle\Database\Database;
use Psr\Container\ContainerInterface;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\Repository\EntityRepository;

final class RepositoryServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            EntityRepository::class => static function (ContainerInterface $container) {
                /** @var Database $database */
                $database = $container->get(Database::class);

                return new DatabaseEntityRepositoryUsingCycle($database);
            },
        ];
    }

    public static function getExtensions(): array
    {
        return [];
    }
}
