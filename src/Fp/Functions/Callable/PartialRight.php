<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hooks\PartialFunctionReturnTypeProvider;

/**
 * Partial application from last function argument
 *
 * Given callable(int, bool, string): bool
 * And "string", true as arguments
 * Will return callable(int): bool
 *
 * REPL:
 * >>> $callback = fn(int $a, string $b, bool $c): bool => true;
 * => callable(int, string, bool): bool
 * >>> partial($callback, true, "string");
 * => callable(int): bool
 *
 * @see PartialFunctionReturnTypeProvider
 */
function partialRight(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($freeArgs, array_reverse($args)));
}

