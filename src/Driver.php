<?php

declare(strict_types=1);

namespace Zorachka\Database;

enum Driver: string
{
    case PGSQL = 'pgsql';
}
