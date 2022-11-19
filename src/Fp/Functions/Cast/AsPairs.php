<?php

declare(strict_types=1);

namespace Fp\Cast;

use Generator;

/**
 * ```php
 * >>> asPairs(['a' => 1, 'b' => 2]);
 * => [['a', 1], ['b', 2]]
 * ```
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return list<array{TK, TV}>
 * @psalm-return ($collection is non-empty-array
 *     ? non-empty-list<array{TK, TV}>
 *     : list<array{TK, TV}>)
 */
function asPairs(iterable $collection): array
{
    return asList(asPairsGenerator($collection));
}

/**
 * ```php
 * >>> fromPairs([['a', 1], ['b', 2]]);
 * => ['a' => 1, 'b' => 2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<array{TK, TV}> $pairs
 * @return array<TK, TV>
 * @psalm-return ($pairs is non-empty-array
 *     ? non-empty-array<TK, TV>
 *     : array<TK, TV>)
 */
function fromPairs(iterable $pairs): array
{
    $array = [];

    foreach ($pairs as [$k, $v]) {
        $array[$k] = $v;
    }

    return $array;
}

/**
 * ```php
 * >>> iterator_to_array(asPairsGenerator(['a' => 1, 'b' => 2]));
 * => [['a', 1], ['b', 2]]
 * ```
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return Generator<int, array{TK, TV}>
 */
function asPairsGenerator(iterable $collection): Generator
{
    return asGenerator(function () use ($collection) {
        foreach ($collection as $key => $value) {
            yield [$key, $value];
        }
    });
}
