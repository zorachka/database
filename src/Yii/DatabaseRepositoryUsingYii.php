<?php

declare(strict_types=1);

namespace Zorachka\Database\Yii;

use RuntimeException;
use Throwable;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Exception\InvalidConfigException;
use Yiisoft\Db\Query\Query;
use Zorachka\Database\Repository\EntityRepository;
use Zorachka\Database\Repository\Exception\CouldNotGetEntityById;
use Zorachka\Database\Repository\Exception\CouldNotSaveEntity;

final class DatabaseRepositoryUsingYii implements EntityRepository
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {}

    public function getById(string $id, string $from): array
    {
        try {
            $data = (new Query($this->connection))
                ->select('*')
                ->from($from)
                ->where(['id' => $id])
                ->one();

            if (!is_array($data)) {
                throw new RuntimeException('Array of data is empty');
            }

            return $data;
        } catch (Throwable $exception) {
            throw CouldNotGetEntityById::withId($id, $exception->getMessage());
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws Exception
     */
    public function hasById(string $id, string $in): bool
    {
        return (new Query($this->connection))
            ->select('*')
            ->from($in)
            ->where(['id' => $id])
            ->exists();
    }

    public function save(array $data, string $to): void
    {
        try {
            $command = $this->connection->createCommand();
            $command->insert($to, $data)->execute();
        } catch (Throwable $exception) {
            throw CouldNotSaveEntity::withReason($exception->getMessage());
        }
    }

    public function update(array $data, string $in, array $condition): void
    {
        try {
            $command = $this->connection->createCommand();
            $command->update($in, $data, $condition)->execute();
        } catch (Throwable $exception) {
            throw CouldNotSaveEntity::withReason($exception->getMessage());
        }
    }
}
