<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database;

final class MigrationsConfig
{
    private string $directory;
    private string $table;
    private bool $safe;

    private function __construct(string $directory, string $table, bool $safe)
    {
        $this->directory = $directory;
        $this->table = $table;
        $this->safe = $safe;
    }

    public static function withDefaults(
        string $directory = '@migrations',
        string $table = 'migrations',
        bool $safe = true,
    ) {
        return new self($directory, $table, $safe);
    }

    /**
     * @return string
     */
    public function directory(): string
    {
        return $this->directory;
    }

    /**
     * @return string
     */
    public function table(): string
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function safe(): bool
    {
        return $this->safe;
    }
}
