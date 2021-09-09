<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hook\PartialFunctionReturnTypeProvider;

/**
 * Partial application from first function argument
 *
 * Given callable(int, bool, string): bool
 * And 1, true as arguments
 * Will return callable(string): bool
 *
 * REPL:
 * >>> $callback = fn(int $a, string $b, bool $c): bool => true;
 * => callable(int, string, bool): bool
 * >>> partialLeft($callback, 1, "string");
 * => callable(bool): bool
 *
 * @see PartialFunctionReturnTypeProvider
 */
function partialLeft(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($args, $freeArgs));
}
