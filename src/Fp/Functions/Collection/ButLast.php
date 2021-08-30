<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns every collection elements except last one
 *
 * REPL:
 * >>> butLast(['a' => 1, 2, 3]);
 * => ['a' => 1, 2]
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return ($collection is list ? list<TV> : array<TK, TV>)
 */
function butLast(iterable $collection): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = $element;
    }

    array_pop($aggregation);

    return $aggregation;
}
