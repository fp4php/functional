<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\PluckFunctionReturnTypeProvider;

/**
 * Map every collection element into given property/key value
 *
 * Given [['a' => 1], ['a' => 2]] and 'a' as key
 * Returns [1, 2]
 *
 * @psalm-param iterable<array-key, object|array> $collection
 *
 * @psalm-return array
 *
 * @psalm-suppress MixedAssignment
 *
 * @see PluckFunctionReturnTypeProvider
 */
function pluck(iterable $collection, string $key): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $value = match (true) {
            (is_object($element)) => $element->{$key} ?? null,
            (is_array($element)) => $element[$key] ?? null,
        };

        $aggregation[$index] = $value;
    }

    return $aggregation;
}
