<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * Try copy and cast collection to non-empty-list
 * Returns None if there is no first collection element
 *
 * REPL:
 * >>> $collection;
 * => iterable<string, int>
 * >>> asNonEmptyList($collection);
 * => Option<non-empty-list<int>>
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return Option<non-empty-list<TV>>
 */
function asNonEmptyList(iterable $collection): Option
{
    /** @psalm-var Option<non-empty-list<TV>> */
    return head($collection)
        ->map(fn() => asList($collection));
}
