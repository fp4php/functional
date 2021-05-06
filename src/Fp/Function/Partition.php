<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool ...$predicates
 *
 * @psalm-return array<array-key, array<TK, TV>>
 */
function partition(iterable $collection, callable ...$predicates): array
{
    $predicateCount = count($predicates);
    $partitions = array_fill(0, $predicateCount + 1, []);

    foreach ($collection as $index => $element) {
        foreach ($predicates as $partition => $callback) {
            if (call_user_func($callback, $element, $index)) {
                $partitions[$partition][$index] = $element;
                continue 2;
            }
        }

        $partitions[$predicateCount][$index] = $element;
    }

    return $partitions;
}

