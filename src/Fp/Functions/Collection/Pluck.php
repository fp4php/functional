<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\Hook\FunctionReturnTypeProvider\PluckFunctionReturnTypeProvider;

/**
 * Map every collection element into given property/key value
 *
 * ```php
 * >>> pluck([['a' => 1], ['a' => 2]], 'a');
 * => [1, 2]
 *
 * >>> pluck([new Foo(1), new Foo(2)], 'a');
 * => [1, 2]
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV of object|array
 * @psalm-param iterable<TK, TV> $collection
 * @see PluckFunctionReturnTypeProvider
 */
function pluck(iterable $collection, string $key): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        /** @psalm-suppress MixedAssignment */
        $aggregation[$index] = match (true) {
            is_array($element) => $element[$key] ?? null,
            is_object($element) => $element->{$key} ?? null,
        };
    }

    return $aggregation;
}
