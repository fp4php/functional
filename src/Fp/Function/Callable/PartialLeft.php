<?php

declare(strict_types=1);

namespace Fp\Function\Callable;

use Fp\Psalm\PartialPlugin;

/**
 * Partial application from first argument
 * @see PartialPlugin
 */
function partialLeft(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($args, $freeArgs));
}
