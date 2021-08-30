<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\NonEmptyArrayList;
use Fp\Functional\Option\Option;

use function Fp\Cast\asNonEmptyList;

/**
 * Reduce multiple elements into one
 * Returns None for empty collection
 *
 * REPL:
 * >>> reduce(
 *     ['a', 'b', 'c'],
 *     fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
 * )->get();
 * => 'abc'
 *
 * @template TK of array-key
 * @template TV
 * @template TVI
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV|TVI, TV): (TV|TVI) $callback (accumulator, current value): new accumulator
 *
 * @return Option<TV|TVI>
 */
function reduce(iterable $collection, callable $callback): Option
{
    return Option::do(function () use ($collection, $callback) {
        $nel = yield asNonEmptyList($collection);
        return NonEmptyArrayList::collectNonEmpty($nel)->reduce($callback);
    });
}
