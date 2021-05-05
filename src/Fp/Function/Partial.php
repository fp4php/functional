<?php

declare(strict_types=1);

namespace Fp\Function;

use Closure;
use Fp\Psalm\PartialPlugin;

/**
 * @see partialLeft alias
 * @see PartialPlugin
 */
function partial(Closure $callback, mixed ...$args): Closure
{
    return partialLeft($callback, $args);
}

/**
 * Partial application from first argument
 * @see PartialPlugin
 */
function partialLeft(Closure $callback, mixed ...$args): Closure
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($args, $freeArgs));
}

/**
 * Partial application from last argument
 * @see PartialPlugin
 */
function partialRight(Closure $callback, mixed ...$args): Closure
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($freeArgs, array_reverse($args)));
}


