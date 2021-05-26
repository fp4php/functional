<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Find if there is element which satisfies the condition
 *
 * @psalm-template TK of array-key
 * @psalm-template TV of (object|scalar|null)
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param (callable(TV, TK): bool)|TV $needle predicate or value
 *
 * @psalm-return bool
 */
function exists(iterable $collection, callable|object|int|float|string|bool|null $needle): bool
{
    /** @psalm-var callable(TV, TK): bool $predicate */
    $predicate = is_callable($needle)
        ? $needle
        : fn(object|int|float|string|bool|null $v): bool => $v === $needle;

    return first($collection, $predicate)->isSome();
}
