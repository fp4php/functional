<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return array<TK, TV>
 */
function reverse(iterable $collection): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = $element;
    }

    return array_reverse($aggregation);
}
