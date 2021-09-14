<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * Try copy and cast collection to non-empty-array
 * Returns None if there is no first collection element
 *
 * REPL:
 * >>> $collection;
 * => iterable<string, int>
 * >>> asNonEmptyArray($collection);
 * => Option<non-empty-array<string, int>>
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param TP $preserveKeys
 * @psalm-return (TP is true ? Option<non-empty-array<TK, TV>> : Option<non-empty-array<int, TV>>)
 */
function asNonEmptyArray(iterable $collection, bool $preserveKeys = true): Option
{
    /** @psalm-var Option<non-empty-array<TK, TV>> */
    return head($collection)
        ->map(fn() => asArray($collection, $preserveKeys));
}
