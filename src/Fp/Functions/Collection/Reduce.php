<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

/**
 * Reduce multiple elements into one
 * Returns None for empty collection
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
 *
 * @psalm-return Option<TV>
 */
function reduce(iterable $collection, callable $callback): Option
{
    $tail = tail($collection);

    return head($collection)
        ->map(function (mixed $head) use ($tail, $callback): mixed {
            /** @var TV $acc */
            $acc = $head;

            foreach ($tail as $element) {
                $acc = call_user_func($callback, $acc, $element);
            }

            return $acc;
        });
}
