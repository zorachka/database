<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database;

use Zorachka\Framework\Console\ConsoleConfig;
use Zorachka\Framework\Container\ServiceProvider;
use Zorachka\Framework\Database\Cycle\Migrations\Console\Migration\CreateCommand;
use Zorachka\Framework\Database\Cycle\Migrations\Console\Migration\DownCommand;
use Zorachka\Framework\Database\Cycle\Migrations\Console\Migration\ListCommand;
use Zorachka\Framework\Database\Cycle\Migrations\Console\Migration\UpCommand;
use Zorachka\Framework\Database\Cycle\Migrations\Event\AfterMigrate;
use Zorachka\Framework\Database\Cycle\Migrations\Event\BeforeMigrate;
use Zorachka\Framework\EventDispatcher\EventDispatcherConfig;
use Zorachka\Framework\EventDispatcher\NullableEventListener;

final class DatabaseServiceProvider implements ServiceProvider
{
    /**
     * @inheritDoc
     */
    public static function getDefinitions(): array
    {
        return [
            DatabaseConfig::class => fn() => DatabaseConfig::withDefaults(),
            MigrationsConfig::class => fn() => MigrationsConfig::withDefaults(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getExtensions(): array
    {
        return [
            ConsoleConfig::class => function(ConsoleConfig $config) {
                return $config
                    ->withCommand(ListCommand::class)
                    ->withCommand(CreateCommand::class)
                    ->withCommand(UpCommand::class)
                    ->withCommand(DownCommand::class);
            },
        ];
    }
}
