<?php

declare(strict_types=1);

namespace Zorachka\Framework\Migrations;

use Zorachka\Framework\Container\ServiceProvider;

final class MigrationsServiceProvider implements ServiceProvider
{
    /**
     * @inheritDoc
     */
    public static function getDefinitions(): array
    {
        return [
            MigrationsConfig::class => fn() => MigrationsConfig::withDefaults(),
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
