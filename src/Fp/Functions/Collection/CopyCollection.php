<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Copy any iterable collection into php array
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return array<TK, TV>
 */
function copyCollection(iterable $collection): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = $element;
    }

    return $aggregation;
}
