<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * Try copy and cast collection to non-empty-array
 * Returns None if there is no first collection element
 *
 * ```php
 * >>> asNonEmptyArray(LinkedList::collect([1, 2]));
 * => Some([1, 2])
 *
 * >>> asNonEmptyArray(LinkedList::collect([]));
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 *
 * @param iterable<TK, TV> $collection
 * @param TP $preserveKeys
 * @return (TP is true ? Option<non-empty-array<TK, TV>> : Option<non-empty-array<int, TV>>)
 */
function asNonEmptyArray(iterable $collection, bool $preserveKeys = true): Option
{
    /** @psalm-var Option<non-empty-array<TK, TV>> */
    return head($collection)
        ->map(fn() => asArray($collection, $preserveKeys));
}
