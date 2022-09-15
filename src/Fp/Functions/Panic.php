<?php

declare(strict_types=1);

namespace Fp;

use Closure;
use RuntimeException;

/**
 * @return Closure(): never
 */
function panic(string $message, float|int|string ...$args): Closure
{
    return fn() => throw new RuntimeException(sprintf($message, ...$args));
}
