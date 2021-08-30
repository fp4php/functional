<?php

declare(strict_types=1);

namespace Fp\Collection;

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
 * @template TA of TV
 *
 * @param TA $init initial accumulator value
 * @param iterable<TK, TV> $collection
 * @param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
 *
 * @return TV
 */
function fold(mixed $init, iterable $collection, callable $callback): mixed
{
    $acc = $init;

    foreach ($collection as $element) {
        $acc = $callback($acc, $element);
    }

    return $acc;
}
