<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * Copy iterable as array
 *
 * ```php
 * >>> asArray(LinkedList::collect([1, 2]));
 * => [1, 2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collections
 * @param TP $preserveKeys
 * @return (
 *     $collections is non-empty-array
 *         ? non-empty-array<TK, TV>
 *         : array<TK, TV>
 * )
 *
 * @no-named-arguments
 */
function asArray(iterable ...$collections): array
{
    $aggregate = [];

    foreach ($collections as $collection) {
        foreach ($collection as $index => $element) {
            $aggregate[$index] = $element;
        }
    }

    return $aggregate;
}
