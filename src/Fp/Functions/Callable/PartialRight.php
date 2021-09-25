<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartialFunctionReturnTypeProvider;

/**
 * Partial application from last function argument
 *
 * Given callable(int, bool, string): bool
 * And "string", true as arguments
 * Will return callable(int): bool
 *
 * ```php
 * >>> $f = fn(int $a, string $b, bool $c): bool => true;
 * >>> partialRight($f, true, "string");
 * => fn(int $a) => $f($a, "string", true)
 * ```
 *
 * {@see PartialFunctionReturnTypeProvider}
 * @psalm-pure
 */
function partialRight(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($freeArgs, array_reverse($args)));
}

