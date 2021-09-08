<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * Copy one or multiple collections as list
 *
 * REPL:
 * >>> asList([1], ['prop' => 2], [3, 4]);
 * => [1, 2, 3, 4]
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> ...$collections
 *
 * @psalm-return (
 *     $collections is non-empty-array
 *         ? non-empty-list<TV>
 *         : list<TV>
 * )
 */
function asList(iterable ...$collections): array
{
    $aggregate = [];

    foreach ($collections as $collection) {
        foreach ($collection as $element) {
            $aggregate[] = $element;
        }
    }

    return $aggregate;
}

/**
 * Copy collection as list of key-value pairs
 *
 * REPL:
 * >>> asListOfPairs(['a' => 1, 'b' => 2]);
 * => [['a', 1], ['b', 2]]
 *
 *
 * @psalm-template TK
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return (
 *     $collection is non-empty-array
 *         ? non-empty-list<array{TK, TV}>
 *         : list<array{TK, TV}>
 * )
 */
function asListOfPairs(iterable $collection): array
{
    $buffer = [];

    foreach ($collection as $key => $value) {
        $buffer[] = [$key, $value];
    }

    return $buffer;
}
