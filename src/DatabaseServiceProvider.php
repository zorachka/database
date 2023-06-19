<?php

declare(strict_types=1);

namespace Zorachka\Database;

use Zorachka\Container\ServiceProvider;
use Zorachka\Database\Event\AfterMigrate;
use Zorachka\Database\Event\BeforeMigrate;
use Zorachka\EventDispatcher\EventDispatcherConfig;
use Zorachka\EventDispatcher\NullableEventListener;

final class DatabaseServiceProvider implements ServiceProvider
{
    public static function getDefinitions(): array
    {
        return [
            DatabaseConfig::class => static fn () => DatabaseConfig::withDefaults(),
            MigrationsConfig::class => static fn () => MigrationsConfig::withDefaults(),
        ];
    }

    public static function getExtensions(): array
    {
        return [
            EventDispatcherConfig::class => static function (
                EventDispatcherConfig $config
            ) {
                return $config
                    ->withEventListener(
                        BeforeMigrate::class,
                        NullableEventListener::class
                    )
                    ->withEventListener(
                        AfterMigrate::class,
                        NullableEventListener::class
                    );
            },
        ];
    }
}
