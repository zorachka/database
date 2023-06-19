<?php

declare(strict_types=1);

namespace Zorachka\Database;

use Webmozart\Assert\Assert;

final class MigrationsConfig
{
    /**
     * @var non-empty-string
     */
    private string $directory;
    /**
     * @var non-empty-string
     */
    private string $namespace;
    /**
     * @var non-empty-string
     */
    private string $table;
    private bool $isSafe;
    /**
     * @var non-empty-string
     */
    private string $customTemplatePath;
    /**
     * @var array<class-string>
     */
    private array $aggregateClasses;

    /**
     * @param non-empty-string $directory
     * @param non-empty-string $namespace
     * @param non-empty-string $table
     * @param non-empty-string $customTemplatePath
     * @param array<class-string> $aggregateClasses
     */
    private function __construct(
        string $directory,
        string $namespace,
        string $table,
        bool $isSafe,
        string $customTemplatePath,
        array $aggregateClasses,
    ) {
        Assert::notEmpty($directory);
        Assert::notEmpty($namespace);
        Assert::notEmpty($table);

        $this->directory = $directory;
        $this->namespace = $namespace;
        $this->table = $table;
        $this->isSafe = $isSafe;
        $this->customTemplatePath = $customTemplatePath;
        $this->aggregateClasses = $aggregateClasses;
    }

    /**
     * @param non-empty-string $directory
     * @param non-empty-string $namespace
     * @param non-empty-string $table
     * @param non-empty-string $customTemplatePath
     * @param array<class-string> $aggregateClasses
     */
    public static function withDefaults(
        string $directory = '@migrations',
        string $namespace = 'Migrations',
        string $table = 'migrations',
        bool $isSafe = true,
        string $customTemplatePath = __DIR__ . '/Doctrine/Migrations/custom_migration_template.tpl',
        array $aggregateClasses = [],
    ): self {
        return new self($directory, $namespace, $table, $isSafe, $customTemplatePath, $aggregateClasses);
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

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function withNamespace(string $namespace): self
    {
        Assert::notEmpty($namespace);
        $new = clone $this;
        $new->namespace = $namespace;

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

    public function customTemplatePath(): string
    {
        return $this->customTemplatePath;
    }

    public function withCustomTemplatePath(string $customTemplatePath): self
    {
        Assert::notEmpty($customTemplatePath);

        $new = clone $this;
        $new->customTemplatePath = $customTemplatePath;

        return $new;
    }

    /**
     * @return array<class-string>
     */
    public function aggregateClasses(): array
    {
        return $this->aggregateClasses;
    }

    /**
     * @param class-string $aggregateClassName
     * @return $this
     */
    public function withAggregateClass(string $aggregateClassName): self
    {
        $new = clone $this;
        $new->aggregateClasses[] = $aggregateClassName;

        return $new;
    }
}
