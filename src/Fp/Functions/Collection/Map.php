<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param callable(TVI, TK): TVO $callback
 *
 * @psalm-return array<TK, TVO>
 */
function map(iterable $collection, callable $callback): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = call_user_func($callback, $element, $index);
    }

    return $aggregation;
}
