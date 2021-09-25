<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartialFunctionReturnTypeProvider;

/**
 * Partial application from first function argument
 *
 * Given callable(int, bool, string): bool
 * And 1, true as arguments
 * Will return callable(string): bool
 *
 * ```php
 * >>> $f = fn(int $a, string $b, bool $c): bool => true;
 * >>> partialLeft($f, 1, "string");
 * => fn(bool $c) => $f(1, "string", $c)
 * ```
 *
 * {@see PartialFunctionReturnTypeProvider}
 * @psalm-pure
 */
function partialLeft(callable $callback, mixed ...$args): callable
{
    return fn(mixed ...$freeArgs): mixed => $callback(...array_merge($args, $freeArgs));
}
