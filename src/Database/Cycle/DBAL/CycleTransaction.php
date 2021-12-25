<?php

declare(strict_types=1);

namespace Zorachka\Framework\Database\Cycle\DBAL;

use Cycle\Database\Database;
use Zorachka\Framework\Database\Transaction;

final class CycleTransaction implements Transaction
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     */
    public function begin(): void
    {
        $this->database->begin();
    }

    /**
     * @inheritDoc
     */
    public function commit(): void
    {
        $this->database->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): void
    {
        $this->database->rollback();
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function transactional(callable $callback): mixed
    {
        return $this->database->transaction($callback);
    }
}
