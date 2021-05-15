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
 * @psalm-return bool
 */
function any(iterable $collection, callable $predicate): bool
{
    return !(first($collection, $predicate)->isEmpty());
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return bool
 */
function anyOf(iterable $collection, string $fqcn, bool $invariant = false): bool
{
    return firstOf($collection, $fqcn, $invariant)->fold(
        fn() => true,
        fn() => false,
    );
}
