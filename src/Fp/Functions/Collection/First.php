<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\FirstOfOperation;
use Fp\Operations\FirstOperation;

/**
 * Find first element which satisfies the condition
 *
 * ```php
 * >>> first([1, 2], fn(int $v): bool => $v === 2)->get()
 * => 1
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param null|callable(TV, TK): bool $predicate
 * @psalm-return Option<TV>
 */
function first(iterable $collection, ?callable $predicate = null): Option
{
    return FirstOperation::of($collection)($predicate);
}

/**
 * Find first element of given class
 *
 * ```php
 * >>> firstOf([1, new Foo(1), new Foo(2)], Foo::class)->get()
 * => Foo(1)
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 * @psalm-return Option<TVO>
 */
function firstOf(iterable $collection, string $fqcn, bool $invariant = false): Option
{
    return FirstOfOperation::of($collection)($fqcn, $invariant);
}
