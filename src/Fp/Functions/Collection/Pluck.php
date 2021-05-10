<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\PluckFunctionReturnTypeProvider;

/**
 * @see PluckFunctionReturnTypeProvider
 *
 * @psalm-suppress MixedAssignment
 *
 * @psalm-param iterable<array-key, object|array> $collection
 *
 * @psalm-return array
 */
function pluck(iterable $collection, string $key): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $value = match (true) {
            (is_object($element)) => $element->{$key},
            (is_array($element)) => $element[$key],
        } ?? null;

        $aggregation[$index] = $value;
    }

    return $aggregation;
}
