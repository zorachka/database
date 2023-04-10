<?php

declare(strict_types=1);

namespace Zorachka\Database;

use Zorachka\Console\ConsoleConfig;
use Zorachka\Container\ServiceProvider;
use Zorachka\Database\Cycle\Migrations\Console\Migration\CreateCommand;
use Zorachka\Database\Cycle\Migrations\Console\Migration\DownCommand;
use Zorachka\Database\Cycle\Migrations\Console\Migration\ListCommand;
use Zorachka\Database\Cycle\Migrations\Console\Migration\UpCommand;

final class DatabaseServiceProvider implements ServiceProvider
{
    /**
     *
     */
    public static function getDefinitions(): array
    {
        return [
            DatabaseConfig::class => static fn () => DatabaseConfig::withDefaults(),
            MigrationsConfig::class => static fn () => MigrationsConfig::withDefaults(),
        ];
    }

    /**
     *
     */
    public static function getExtensions(): array
    {
        return [
            ConsoleConfig::class => static function (ConsoleConfig $config) {
                return $config
                    ->withCommand(ListCommand::class)
                    ->withCommand(CreateCommand::class)
                    ->withCommand(UpCommand::class)
                    ->withCommand(DownCommand::class);
            },
        ];
    }
}
