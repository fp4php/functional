<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\Hooks\PluckFunctionReturnTypeProvider;

/**
 * Map every collection element into given property/key value
 *
 * REPL:
 * >>> pluck([['a' => 1], ['a' => 2]], 'a');
 * => [1, 2]
 *
 * @deprecated use {@see map()} function
 * @psalm-param iterable<array-key, object|array> $collection
 * @psalm-return array
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
