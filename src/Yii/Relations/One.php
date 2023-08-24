<?php

declare(strict_types=1);

namespace Zorachka\Database\Yii\Relations;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\Query;

final class One
{
    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {}

    public function hasOne(): Query
    {
        $query = new Query($this->connection);
    }

    public function hasMany(): Query
    {

    }

    public function belongsTo(): Query
    {

    }
}
