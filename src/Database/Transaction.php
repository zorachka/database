<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database;

interface Transaction
{
    /**
     * Begin DB transaction.
     */
    public function begin(): void;

    /**
     * Commit DB transaction.
     */
    public function commit(): void;

    /**
     * Rollback DB transaction.
     */
    public function rollback(): void;

    /**
     * Manage state of your transaction automatically.
     * @param callable $callback
     * @return mixed
     */
    public function transactional(callable $callback): mixed;
}
