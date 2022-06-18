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
 * @template TP of bool
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TV> : list<TV>)
 */
function asArray(iterable $collection, bool $preserveKeys = true): array
{
    $aggregate = [];

    foreach ($collection as $index => $element) {
        if ($preserveKeys) {
            $aggregate[$index] = $element;
        } else {
            $aggregate[] = $element;
        }
    }

    return $aggregate;
}
