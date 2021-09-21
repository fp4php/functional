<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Find if there is element which satisfies the condition
 * false otherwise
 *
 * ```php
 * >>> exists([1, 2], fn(int $v): bool => $v === 1);
 * => true
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $predicate
 * @psalm-return bool
 */
function exists(iterable $collection, callable $predicate): bool
{
    return first($collection, $predicate)->isSome();
}

/**
 * Returns true if there is collection element of given class
 * False otherwise
 *
 * ```php
 * >>> existsOf([new Foo(), 2, 3], Foo::class);
 * => true
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
function existsOf(iterable $collection, string $fqcn, bool $invariant = false): bool
{
    return firstOf($collection, $fqcn, $invariant)->fold(
        fn() => true,
        fn() => false,
    );
}
