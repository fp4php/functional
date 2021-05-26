<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Copy collection in reversed order
 *
 * REPL:
 * >>> reverse([1, 2, 3]);
 * => [3, 2, 1]
 *
 *
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
