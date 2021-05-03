<?php

declare(strict_types=1);

namespace Fp\Function;

use Fp\Psalm\PartialPlugin;

/**
 * @see partialLeft alias
 * @see PartialPlugin
 */
function partial(callable $callback, mixed ...$args): callable
{
    return partialLeft($callback, $args);
}

/**
 * Partial application from first argument
 * @see PartialPlugin
 */
function partialLeft(callable $callback, mixed ...$args): callable
{
    return fn(...$freeArgs) => $callback(...array_merge($args, $freeArgs));
}

/**
 * Partial application from last argument
 * @see PartialPlugin
 */
function partialRight(callable $callback, mixed ...$args): callable
{
    return fn(...$freeArgs) => $callback(...array_merge($freeArgs, array_reverse($args)));
}


