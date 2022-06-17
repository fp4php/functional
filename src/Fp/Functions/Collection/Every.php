<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\EveryOfOperation;
use Fp\Operations\EveryOperation;

/**
 * Returns true if every collection element satisfies the condition
 * false otherwise
 *
 * ```php
 * >>> every([1, 2], fn(int $v) => $v === 1);
 * => false
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 * @psalm-return bool
 */
function every(iterable $collection, callable $predicate): bool
{
    return EveryOperation::of($collection)($predicate);
}

/**
 * Returns true if every collection element is of given class
 * false otherwise
 *
 * ```php
 * >>> everyOf([1, new Foo()], Foo::class);
 * => false
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 * @psalm-return bool
 */
function everyOf(iterable $collection, string $fqcn, bool $invariant = false): bool
{
    return EveryOfOperation::of($collection)($fqcn, $invariant);
}
