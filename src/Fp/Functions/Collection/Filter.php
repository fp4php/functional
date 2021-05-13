<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 *
 * @psalm-return array<TK, TV>
 */
function filter(iterable $collection, callable $predicate): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        if (call_user_func($predicate, $element, $index)) {
            $aggregation[$index] = $element;
        }
    }

    return $aggregation;
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV|null> $collection
 *
 * @psalm-return array<TK, TV>
 */
function filterNotNull(iterable $collection): array
{
    return filter($collection, fn(mixed $v) => !is_null($v));
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV of object
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return array<TK, TVO>
 */
function filterInstancesOf(iterable $collection, string $fqcn): array
{
    /** @var array<TK, TVO> $instances */
    $instances = filter(
        $collection,
        fn(mixed $v): bool => is_a($v, $fqcn, true)
    );

    return $instances;
}

