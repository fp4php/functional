<?php

declare(strict_types=1);

namespace Fp\Collection;

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
 * @template K of array-key
 * @template A
 * @template KOut of array-key
 * @template B
 *
 * @param iterable<K, A> $collection
 * @param callable(A): KOut $group
 * @param callable(A): B $map
 * @param callable(B, B): B $reduce
 * @return array<KOut, B>
 */
function groupMapReduce(iterable $collection, callable $group, callable $map, callable $reduce): array
{
    $grouped = [];

    foreach ($collection as $item) {
        $key = $group($item);

        if (array_key_exists($key, $grouped)) {
            $grouped[$key] = $reduce($grouped[$key], $map($item));
        } else {
            $grouped[$key] = $map($item);
        }
    }

    return $grouped;
}
