<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @template TK of array-key
 * @template TVI
 * @template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param \Closure(TVI, TK): TVO $callback
 * @psalm-return array<TK, TVO>
 */
function map(iterable $collection, callable $callback): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = $callback($element, $index);
    }

    return $aggregation;
}
