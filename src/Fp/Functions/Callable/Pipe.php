<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Psalm\Hook\DynamicFunctionStorageProvider\PipeFunctionStorageProvider;

/**
 * Pipes the value of an expression into a pipeline of functions.
 *
 * ```php
 * >>> pipe(
 * >>>     0,
 * >>>     fn($i) => $i + 11,
 * >>>     fn($i) => $i + 20,
 * >>>     fn($i) => $i + 11,
 * >>> );
 * => 42
 * ```
 *
 * @see PipeFunctionStorageProvider
 * @no-named-arguments
 */
function pipe(mixed $a, callable $head, callable ...$tail): mixed
{
    foreach ([$head, ...$tail] as $function) {
        /** @psalm-suppress MixedAssignment */
        $a = $function($a);
    }

    return $a;
}
