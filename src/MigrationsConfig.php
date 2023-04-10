<?php

declare(strict_types=1);

namespace Zorachka\Database;

use Webmozart\Assert\Assert;

final class MigrationsConfig
{
    private string $directory;
    private string $table;
    private bool $isSafe;

    private function __construct(string $directory, string $table, bool $isSafe)
    {
        $this->directory = $directory;
        $this->table = $table;
        $this->isSafe = $isSafe;
    }

    public static function withDefaults(
        string $directory = '@migrations',
        string $table = 'migrations',
        bool $isSafe = true,
    ): self {
        return new self($directory, $table, $isSafe);
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function withDirectory(string $directory): self
    {
        Assert::notEmpty($directory);
        $new = clone $this;
        $new->directory = $directory;

        return $new;
    }

    public function table(): string
    {
        return $this->table;
    }

    public function withTable(string $table): self
    {
        Assert::notEmpty($table);
        $new = clone $this;
        $new->table = $table;

        return $new;
    }

    public function isSafe(): bool
    {
        return $this->isSafe;
    }

    public function withSafe(bool $isSafe): self
    {
        Assert::notEmpty($isSafe);
        $new = clone $this;
        $new->isSafe = $isSafe;

        return $new;
    }
}
