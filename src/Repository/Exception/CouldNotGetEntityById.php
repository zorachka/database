<?php

declare(strict_types=1);

namespace Zorachka\Database\Repository\Exception;

use RuntimeException;

final class CouldNotGetEntityById extends RuntimeException
{
    public static function withId(
        string $id,
        string $reason,
    ): self {
        return new self(
            sprintf(
                'Could not find an entity with ID "%s", because: "%s"',
                $id,
                $reason,
            )
        );
    }
}
