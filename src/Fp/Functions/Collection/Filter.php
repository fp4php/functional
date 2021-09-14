<?php

declare(strict_types=1);

namespace Fp\Collection;

use function Fp\of;

/**
 * Filter collection by condition
 * Do not preserve keys by default
 *
 * REPL:
 * >>> filter([1, 2], fn(int $v): bool => $v === 2);
 * => [2]
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 * @psalm-param TP $preserveKeys
 * @psalm-return (TP is true ? array<TK, TV> : list<TV>)
 */
function filter(iterable $collection, callable $predicate, bool $preserveKeys = false): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        if ($predicate($element, $index)) {
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
 * Do not preserve keys by default
 *
 * REPL:
 * >>> filterNotNull([1, null, 2]);
 * => [1, 2]
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 * @psalm-param iterable<TK, TV|null> $collection
 * @psalm-param TP $preserveKeys
 * @psalm-return (TP is true ? array<TK, TV> : list<TV>)
 */
function filterNotNull(iterable $collection, bool $preserveKeys = false): array
{
    return filter(
        $collection,
        fn(mixed $v) => !is_null($v),
        $preserveKeys
    );
}

/**
 * Filter elements of given class
 * Do not preserve keys by default
 *
 * REPL:
 * >>> filterOf([1, new Foo(), 2], Foo::class);
 * => list<Foo>
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 * @psalm-template TP of bool
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param TP $preserveKeys
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 * @psalm-return (TP is true ? array<TK, TVO> : list<TVO>)
 */
function filterOf(iterable $collection, string $fqcn, bool $preserveKeys = false, bool $invariant = false): array
{
    /** @var array<TK, TVO> */
    return filter(
        $collection,
        fn(mixed $v): bool => of($v, $fqcn, $invariant),
        $preserveKeys
    );
}

