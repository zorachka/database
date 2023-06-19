<?php

declare(strict_types=1);

namespace Zorachka\Database\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class AggregateSchemaProvider implements SchemaProvider
{
    /**
     * @var string[]
     *
     * @phpstan-var array<class-string>
     */
    private array $aggregateClasses;

    /**
     * @param string[] $aggregateClasses
     *
     * @phpstan-param array<class-string> $aggregateClasses
     */
    public function __construct(array $aggregateClasses)
    {
        Assert::allString($aggregateClasses);
        $this->aggregateClasses = $aggregateClasses;
    }

    public function createSchema(): Schema
    {
        $schema = new Schema();

        foreach ($this->aggregateClasses as $aggregateClass) {
            if (!is_a($aggregateClass, SpecifiesSchema::class, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Class "%s" was expected to implement "%s"',
                        $aggregateClass,
                        SpecifiesSchema::class
                    )
                );
            }

            $aggregateClass::specifySchema($schema);
        }

        return $schema;
    }
}
