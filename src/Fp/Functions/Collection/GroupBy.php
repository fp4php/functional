<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Group collection elements by key returned by function
 *
 * REPL:
 * >>> group(
 *     [1, 2, 3],
 *     fn(int $v): int => $v
 * );
 * => [1 => [1], 2 => [2], 3 => [3]]
 *
 *
 * @psalm-template TKG of array-key
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): TKG $callback
 *
 * @psalm-return (
 *		$collection is non-empty-array
 *          ? non-empty-array<TKG, non-empty-array<TK, TV>>
 *          : array<TKG, array<TK, TV>>
 * )
 */
function groupBy(iterable $collection, callable $callback): array
{
    $groups = [];

    foreach ($collection as $index => $element) {
        $groupKey = $callback($element, $index);

        if (!isset($groups[$groupKey])) {
            $groups[$groupKey] = [];
        }

        $groups[$groupKey][$index] = $element;
    }

    return $groups;
}

