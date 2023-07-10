<?php

declare(strict_types=1);

namespace Zorachka\Database\Repository;

use Zorachka\Database\Repository\Exception\CouldNotGetEntityById;
use Zorachka\Database\Repository\Exception\CouldNotSaveEntity;

interface EntityRepository
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $from
     * @return array<string, string>
     * @throws CouldNotGetEntityById
     */
    public function getById(string $id, string $from): array;

    /**
     * @param non-empty-string $id
     * @param non-empty-string $in
     */
    public function hasById(string $id, string $in): bool;

    /**
     * @param array<string, string|int|bool|null> $data
     * @param non-empty-string $to
     * @return void
     * @throws CouldNotSaveEntity
     */
    public function save(array $data, string $to): void;

    /**
     * @param array<string, string|int|bool|null> $data
     * @param non-empty-string $in
     * @return void
     */
    public function update(array $data, string $in): void;
}