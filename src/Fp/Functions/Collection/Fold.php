<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\ArrayList;

use function Fp\Cast\asList;

/**
 * Fold many elements into one
 *
 * REPL:
 * >>> fold(
 *     '',
 *     ['a', 'b', 'c'],
 *     fn(string $accumulator, $currentValue) => $accumulator . $currentValue
 * )
 * => 'abc'
 *
 * @template TK of array-key
 * @template TV
 * @template TA
 *
 * @param TA $init initial accumulator value
 * @param iterable<TK, TV> $collection
 * @param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
 *
 * @return TA
 */
function fold(mixed $init, iterable $collection, callable $callback): mixed
{
    return ArrayList::collect(asList($collection))->fold($init, $callback);
}
