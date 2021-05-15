<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * Copy one or multiple collections as list
 *
 * Given [1], ['prop' => 2], [3, 4]
 * Returns [1, 2, 3, 4]
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> ...$collections
 *
 * @psalm-return list<TV>
 */
function asList(iterable ...$collections): array
{
    $aggregate = [];

    foreach ($collections as $collection) {
        foreach ($collection as $element) {
            $aggregate[] = $element;
        }
    }

    return $aggregate;
}
