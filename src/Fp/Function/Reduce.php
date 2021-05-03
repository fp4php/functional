<?php

declare(strict_types=1);

namespace Fp\Function;

use Fp\Functional\Option\Option;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param \Closure(TV, TV): TV $callback
 *
 * @psalm-return Option<TV>
 */
function reduce(iterable $collection, \Closure $callback): Option
{
    $tail = tail($collection);

    return head($collection)
        ->map(function (mixed $head) use ($tail, $callback): mixed {
            /** @var TV $acc */
            $acc = $head;

            foreach ($tail as $element) {
                $acc = $callback($acc, $element);
            }

            return $acc;
        });
}
