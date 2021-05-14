<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 * @psalm-param TP $preserveKeys
 *
 * @psalm-return (TP is true ? array<TK, TV> : list<TV>)
 */
function filter(iterable $collection, callable $predicate, bool $preserveKeys = true): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        if (call_user_func($predicate, $element, $index)) {
            if ($preserveKeys) {
                $aggregation[$index] = $element;
            } else {
                $aggregation[] = $element;
            }
        }
    }

    return $aggregation;
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 *
 * @psalm-param iterable<TK, TV|null> $collection
 * @psalm-param TP $preserveKeys
 *
 * @psalm-return (TP is true ? array<TK, TV> : list<TV>)
 */
function filterNotNull(iterable $collection, bool $preserveKeys = true): array
{
    return filter(
        $collection,
        fn(mixed $v) => !is_null($v),
        $preserveKeys
    );
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 * @psalm-template TP of bool
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 * @psalm-param TP $preserveKeys
 *
 * @psalm-return (TP is true ? array<TK, TVO> : list<TVO>)
 */
function filterInstancesOf(iterable $collection, string $fqcn, bool $preserveKeys = true): array
{
    /** @var array<TK, TVO> $instances */
    $instances = filter(
        $collection,
        fn(mixed $v): bool => is_a($v, $fqcn, true),
        $preserveKeys
    );

    return $instances;
}

