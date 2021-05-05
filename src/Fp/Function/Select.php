<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $callback
 *
 * @psalm-return array<TK, TV>
 */
function select(iterable $collection, callable $callback): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        if (call_user_func($callback, $element, $index)) {
            $aggregation[$index] = $element;
        }
    }

    return $aggregation;
}

