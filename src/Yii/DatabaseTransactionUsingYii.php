<?php

declare(strict_types=1);

namespace Zorachka\Database\Yii;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\InvalidConfigException;
use Yiisoft\Db\Exception\NotSupportedException;
use Yiisoft\Db\Transaction\TransactionInterface;
use Zorachka\Database\Transaction;

final class DatabaseTransactionUsingYii implements Transaction
{
    private ?TransactionInterface $transaction = null;

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {}

    /**
     * @throws InvalidConfigException
     * @throws \Throwable
     * @throws NotSupportedException
     * @throws Exception
     */
    public function begin(): void
    {
        $this->transaction = $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        return;
    }

    /**
     * @throws InvalidConfigException
     * @throws \Throwable
     * @throws Exception
     */
    public function rollback(): void
    {
        $this->transaction->rollBack();
    }

    /**
     * @throws \Throwable
     */
    public function transactionally(callable $callback): void
    {
        $this->connection->transaction($callback);
    }
}
