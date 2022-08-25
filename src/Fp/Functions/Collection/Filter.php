<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\FilterMapOperation;
use Fp\Operations\FilterNotNullOperation;
use Fp\Operations\FilterOfOperation;
use Fp\Operations\FilterOperation;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;
use function Fp\Cast\asList;

/**
 * Filter collection by condition
 * Do not preserve keys by default
 *
 * ```php
 * >>> filter([1, 2], fn(int $v): bool => $v === 2);
 * => [2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): bool $predicate
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TV> : list<TV>)
 */
function filter(iterable $collection, callable $predicate, bool $preserveKeys = false): array
{
    return filterKV($collection, dropFirstArg($predicate), $preserveKeys);
}

/**
 * Same as {@see filter()} but passing also the key to the $predicate function.
 *
 * ```php
 * >>> filterKV(['fst' => 1, 'snd' => 2, 'thd' => 3], fn($k, $v) => $k !== 'fst' && $v !== 3);
 * => [2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TV> : list<TV>)
 */
function filterKV(iterable $collection, callable $predicate, bool $preserveKeys = false): array
{
    $gen = FilterOperation::of($collection)($predicate);
    return $preserveKeys
        ? asArray($gen)
        : asList($gen);
}

/**
 * Filter not null elements
 * Do not preserve keys by default
 *
 * ```php
 * >>> filterNotNull([1, null, 2]);
 * => [1, 2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 *
 * @param iterable<TK, TV|null> $collection
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TV> : list<TV>)
 */
function filterNotNull(iterable $collection, bool $preserveKeys = false): array
{
    $gen = FilterNotNullOperation::of($collection)();
    return $preserveKeys
        ? asArray($gen)
        : asList($gen);
}

/**
 * Filter elements of given class
 * Do not preserve keys by default
 *
 * ```php
 * >>> filterOf([1, new Foo(1), 2], Foo::class);
 * => [Foo(1)]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TVO> : list<TVO>)
 */
function filterOf(iterable $collection, string|array $fqcn, bool $preserveKeys = false, bool $invariant = false): array
{
    $gen = FilterOfOperation::of($collection)($fqcn, $invariant);
    return $preserveKeys
        ? asArray($gen)
        : asList($gen);
}

/**
 * A combined {@see map()} and {@see filter()}.
 *
 * Filtering is handled via Option instead of Boolean.
 * So the output type TVO can be different from the input type TV.
 * Do not preserve keys by default.
 *
 * ```php
 * >>> filterMap([1, 2], fn(int $v): bool => $v === 2 ? Option::some($v) : Option::none());
 * => [2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TP of bool
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Option<TVO> $predicate
 * @param TP $preserveKeys
 * @return (TP is true ? array<TK, TVO> : list<TVO>)
 */
function filterMap(iterable $collection, callable $predicate, bool $preserveKeys = false): array
{
    $gen = FilterMapOperation::of($collection)(dropFirstArg($predicate));
    return $preserveKeys
        ? asArray($gen)
        : asList($gen);
}
