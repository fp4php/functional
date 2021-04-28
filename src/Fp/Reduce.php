<?php

declare(strict_types=1);

namespace Fp;

/**
 * @template TK of array-key
 * @template TV
 *
 * @psalm-param non-empty-array<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback
 * @psalm-return TV
 */
function reduce(iterable $collection, callable $callback): mixed
{
    $acc = array_shift($collection);

    foreach ($collection as $element) {
        $acc = $callback($acc, $element);
    }

    return $acc;
}
