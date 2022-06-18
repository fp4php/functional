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
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return list<array{TK, TV}>
 */
function asPairs(iterable $collection): array
{
    return asList(asGenerator(function () use ($collection) {
        foreach ($collection as $key => $value) {
            yield [$key, $value];
        }
    }));
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
