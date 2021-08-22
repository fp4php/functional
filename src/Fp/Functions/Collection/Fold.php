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
 * @deprecated use {@see Seq::fold()} or {@see Set::fold()} or {@see Map::fold()}
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TA of TV
 *
 * @psalm-param TA $init initial accumulator value
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback (accumulator, current element): new accumulator
 *
 * @psalm-return TV
 */
function fold(mixed $init, iterable $collection, callable $callback): mixed
{
    $acc = $init;

    foreach ($collection as $element) {
        $acc = call_user_func($callback, $acc, $element);
    }

    return $acc;
}
