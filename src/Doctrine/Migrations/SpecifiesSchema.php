<?php

declare(strict_types=1);

namespace Zorachka\Database\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;

interface SpecifiesSchema
{
    public static function specifySchema(Schema $schema): void;
}
