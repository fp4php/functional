<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartialFunctionReturnTypeProvider;

/**
 * Partial application from first function argument.
 *
 * Pass callback and N callback arguments.
 * These N arguments will be locked at corresponding places (callback parameters)
 * from left-side and new callback will be returned with fewer arguments.
 *
 * ```php
 * >>> $f = fn(int $a, string $b, bool $c): bool => true;
 * >>> partial($f, 1, "string");
 * => fn(bool $c) => $f(1, "string", $c)
 * ```
 *
 * Alias for {@see partialLeft}
 *
 * @see PartialFunctionReturnTypeProvider
 */
function partial(callable $callback, mixed ...$args): callable
{
    return partialLeft($callback, ...$args);
}
