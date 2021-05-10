<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\PartialFunctionReturnTypeProvider;

/**
 * Partial application from first argument
 * @see PartialFunctionReturnTypeProvider
 */
function partialLeft(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($args, $freeArgs));
}
