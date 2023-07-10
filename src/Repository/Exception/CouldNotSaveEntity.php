<?php

declare(strict_types=1);

namespace Zorachka\Database\Repository\Exception;

use RuntimeException;

final class CouldNotSaveEntity extends RuntimeException
{
    public static function withReason(
        string $message
    ): self {
        return new self(
            sprintf('Could not save an entity, because of "%s"', $message)
        );
    }
}
