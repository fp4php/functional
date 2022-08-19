<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\Collection;
use Fp\Collections\NonEmptyHashMap;
use Fp\Operations\GroupByOperation;
use function Fp\Callable\dropFirstArg;

/**
 * Group collection elements by key returned by function
 *
 * ```php
 * >>> groupBy(
 *     [1, 2, 3],
 *     fn(int $v): int => $v
 * );
 * => [1 => [1], 2 => [2], 3 => [3]]
 * ```
 *
 * @template TKG of array-key
 * @template TV
 *
 * @param Collection<TV> | iterable<mixed, TV> $collection
 * @param callable(TV): TKG $callback
 * @return (
 *     $collection is non-empty-array
 *          ? non-empty-array<TKG, non-empty-list<TV>>
 *          : array<TKG, non-empty-list<TV>>
 * )
 */
function groupBy(iterable $collection, callable $callback): array
{
    return GroupByOperation::of($collection)(dropFirstArg($callback))
        ->map(fn(NonEmptyHashMap $group) => $group->values()->toNonEmptyList())
        ->toArray();
}
