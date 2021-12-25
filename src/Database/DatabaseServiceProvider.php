<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database;

use Zorachka\Framework\Container\ServiceProvider;

final class DatabaseServiceProvider implements ServiceProvider
{
    /**
     * @inheritDoc
     */
    public static function getDefinitions(): array
    {
        return [
            DatabaseConfig::class => fn() => DatabaseConfig::withDefaults(),
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
