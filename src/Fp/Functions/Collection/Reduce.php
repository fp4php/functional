<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\ReduceOperation;

/**
 * Reduce multiple elements into one
 * Returns None for empty collection
 *
 * ```php
 * >>> reduce(
 *     ['a', 'b', 'c'],
 *     fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
 * )->get();
 * => 'abc'
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TA
 * @param iterable<TK, TV> $collection
 * @param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
 * @return Option<TV|TA>
 */
function reduce(iterable $collection, callable $callback): Option
{
    return ReduceOperation::of($collection)($callback);
}
