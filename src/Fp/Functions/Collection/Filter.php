<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\FilterNotNullOperation;
use Fp\Operations\FilterOfOperation;
use Fp\Operations\FilterOperation;

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
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TP of bool
 * @psalm-param iterable<TK, TV|null> $collection
 * @psalm-param TP $preserveKeys
 * @psalm-return (TP is true ? array<TK, TV> : list<TV>)
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
    $gen = FilterOfOperation::of($collection)($fqcn, $invariant);
    return $preserveKeys
        ? asArray($gen)
        : asList($gen);
}

