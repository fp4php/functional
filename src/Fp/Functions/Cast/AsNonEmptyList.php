<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * Try copy and cast collection to non-empty-list
 * Returns None if there is no first collection element
 *
 * ```php
 * >>> asNonEmptyList(LinkedList::collect([1, 2]));
 * => Some([1, 2])
 *
 * >>> asNonEmptyList(LinkedList::collect([]));
 * => None
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @return Option<non-empty-list<TV>>
 */
function asNonEmptyList(iterable $collection): Option
{
    /** @psalm-var Option<non-empty-list<TV>> */
    return head($collection)
        ->map(fn() => asList($collection));
}
