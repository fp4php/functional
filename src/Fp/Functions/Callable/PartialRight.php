<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\PartialFunctionReturnTypeProvider;

/**
 * Partial application from last argument
 * @see PartialFunctionReturnTypeProvider
 */
function partialRight(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($freeArgs, array_reverse($args)));
}

