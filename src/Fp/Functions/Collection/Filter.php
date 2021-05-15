<?php

declare(strict_types=1);

namespace Fp\Collection;

use function Fp\of;

/**
 * Filter collection by condition
 *
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
 * Filter not null elements
 *
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
 * Filter elements of given class
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 * @psalm-template TP of bool
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param TP $preserveKeys
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return (TP is true ? array<TK, TVO> : list<TVO>)
 */
function filterOf(iterable $collection, string $fqcn, bool $preserveKeys = true, bool $invariant = false): array
{
    /** @var array<TK, TVO> $instances */
    $instances = filter(
        $collection,
        fn(mixed $v): bool => of($v, $fqcn, $invariant),
        $preserveKeys
    );

    return $instances;
}

