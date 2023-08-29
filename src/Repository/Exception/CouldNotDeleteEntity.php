<?php

declare(strict_types=1);

namespace Zorachka\Database\Repository\Exception;

use RuntimeException;

final class CouldNotDeleteEntity extends RuntimeException
{
    public static function withReason(
        string $message
    ): self {
        return new self(
            sprintf('Could not delete an entity, because of "%s"', $message)
        );
    }
}
