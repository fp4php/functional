<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\NonEmptyHashMap;
use Fp\Operations\GroupMapOperation;

use function Fp\Callable\dropFirstArg;

/**
 * Partitions given $collection into an array of groups according to a discriminator function $group.
 * Each element in a group is transformed into a value of type TVO using $map.
 *
 * It is equivalent to:
 * ```
 * $items = [
 *     ['id' => 10, 'sum' => 10],
 *     ['id' => 10, 'sum' => 15],
 *     ['id' => 10, 'sum' => 20],
 *     ['id' => 20, 'sum' => 10],
 *     ['id' => 20, 'sum' => 15],
 *     ['id' => 30, 'sum' => 20],
 * ];
 *
 * return map(
 *     groupBy($items, fn(array $a) => $a['id']),
 *     fn($group) => map($group, fn(array $a) => $a['sum'] + 1)
 * )
 * ```
 *
 * But more efficient and readable:
 * ```
 * $items = [
 *     ['id' => 10, 'sum' => 10],
 *     ['id' => 10, 'sum' => 15],
 *     ['id' => 10, 'sum' => 20],
 *     ['id' => 20, 'sum' => 10],
 *     ['id' => 20, 'sum' => 15],
 *     ['id' => 30, 'sum' => 20],
 * ];
 *
 * return groupMap(
 *     $items,
 *     fn(array $a) => $a['id'],
 *     fn(array $a) => $a['sum'] + 1,
 * )
 *```
 *
 * Result:
 * ```
 * [
 *   10 => [21, 16, 11],
 *   20 => [16, 11],
 *   30 => [21],
 * ]
 * ```
 *
 * @template TV
 * @template TKO of array-key
 * @template TVO
 *
 * @param iterable<TV> $collection
 * @param callable(TV): TKO $group
 * @param callable(TV): TVO $map
 * @return array<TKO, non-empty-list<TVO>>
 *
 * @psalm-return ($collection is non-empty-array
 *     ? non-empty-array<TKO, non-empty-list<TVO>>
 *     : array<TKO, non-empty-list<TVO>>)
 */
function groupMap(iterable $collection, callable $group, callable $map): array
{
    return groupMapKV($collection, dropFirstArg($group), dropFirstArg($map));
}

/**
 * Same as {@see groupMap()} but passing also the key to the $group and $map functions.
 *
 * @template TK
 * @template TV
 * @template TKO of array-key
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): TKO $group
 * @param callable(TK, TV): TVO $map
 * @return array<TKO, non-empty-list<TVO>>
 *
 * @psalm-return ($collection is non-empty-array
 *     ? non-empty-array<TKO, non-empty-list<TVO>>
 *     : array<TKO, non-empty-list<TVO>>)
 */
function groupMapKV(iterable $collection, callable $group, callable $map): array
{
    return GroupMapOperation::of($collection)($group, $map)
        ->map(fn(NonEmptyHashMap $group) => $group->values()->toNonEmptyList())
        ->toArray();
}
