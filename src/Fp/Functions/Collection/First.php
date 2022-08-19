<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\FirstOfOperation;
use Fp\Operations\FirstOperation;
use function Fp\Callable\dropFirstArg;

/**
 * Find first element which satisfies the condition
 *
 * ```php
 * >>> first([1, 2], fn(int $v): bool => $v === 2)->get()
 * => 1
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function first(iterable $collection, ?callable $predicate = null): Option
{
    return FirstOperation::of($collection)(null !== $predicate ? dropFirstArg($predicate) : null);
}

/**
 * Find first element of given class
 *
 * ```php
 * >>> firstOf([1, new Foo(1), new Foo(2)], Foo::class)->get()
 * => Foo(1)
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO> $fqcn fully qualified class name
 * @param bool $invariant if turned on then subclasses are not allowed
 * @return Option<TVO>
 */
function firstOf(iterable $collection, string $fqcn, bool $invariant = false): Option
{
    return FirstOfOperation::of($collection)($fqcn, $invariant);
}
