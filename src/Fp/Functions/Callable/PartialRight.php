<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\PartialPlugin;

/**
 * Partial application from last argument
 * @see PartialPlugin
 */
function partialRight(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($freeArgs, array_reverse($args)));
}

