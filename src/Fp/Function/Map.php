<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param \Closure(TVI, TK): TVO $callback
 *
 * @psalm-return array<TK, TVO>
 */
function map(iterable $collection, \Closure $callback): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = $callback($element, $index);
    }

    return $aggregation;
}
