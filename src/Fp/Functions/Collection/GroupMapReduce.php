<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\GroupMapReduceOperation;

/**
 * Partitions this iterable collection into a map according to a discriminator function key.
 * All the values that have the same discriminator are then transformed by the value function and
 * then reduced into a single value with the reduce function.
 *
 * ```php
 * >>> groupMapReduce(
 *         collection: [
 *             ['id' => 10, 'val' => 10],
 *             ['id' => 10, 'val' => 15],
 *             ['id' => 10, 'val' => 20],
 *             ['id' => 20, 'val' => 10],
 *             ['id' => 20, 'val' => 15],
 *             ['id' => 30, 'val' => 20],
 *         ],
 *         group: fn(array $a) => $a['id'],
 *         map: fn(array $a) => [$a['val']],
 *         reduce: fn(array $old, array $new) => array_merge($old, $new),
 *     );
 * => [10 => [10, 15, 20], 20 => [10, 15], 30 => [20]]
 * ```
 *
 * @template TV
 * @template TKO of array-key
 * @template TVO
 *
 * @param iterable<mixed, TV> $collection
 * @param callable(TV): TKO $group
 * @param callable(TV): TVO $map
 * @param callable(TVO, TVO): TVO $reduce
 * @return array<KOut, TVO>
 *
 * @psalm-return ($collection is non-empty-array
 *     ? non-empty-array<TKO, TVO>
 *     : array<TKO, TVO>)
 */
function groupMapReduce(iterable $collection, callable $group, callable $map, callable $reduce): array
{
    return GroupMapReduceOperation::of($collection)($group, $map, $reduce)->toArray();
}
