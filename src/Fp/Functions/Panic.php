<?php

declare(strict_types=1);

namespace Fp;

use RuntimeException;

/**
 * @return empty
 */
function panic(string $message, float|int|string ...$args): mixed
{
    throw new RuntimeException(sprintf($message, ...$args));
}
