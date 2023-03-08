<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\UniqueByOperation;

use function Fp\Cast\asArray;
use function Fp\Cast\asList;

/**
 * Returns collection unique elements
 *
 * ```php
 * >>> unique([1, 2, 2, 3, 3, 3, 3]);
 * => [1, 2, 3]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @param iterable<TK, TV> $collection
 * @return ($collection is list ? list<TV> : array<TK, TV>)
 */
function unique(iterable $collection): array
{
    return is_array($collection) && array_is_list($collection)
        ? asList(UniqueByOperation::of($collection)(fn(mixed $i): mixed => $i))
        : asArray(UniqueByOperation::of($collection)(fn(mixed $i): mixed => $i));
}

/**
 * Returns collection unique elements by given $callback.
 *
 * ```php
 * >>> unique(
 *     [new User(id: 1), new User(id: 1), new User(id: 2)],
 *     fn(User $user) => $user->getId(),
 * );
 * => [User(1), User(2)]
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @param iterable<TK, TV> $collection
 * @param callable(TV): mixed $callback
 * @return ($collection is list ? list<TV> : array<TK, TV>)
 */
function uniqueBy(iterable $collection, callable $callback): array
{
    return is_array($collection) && array_is_list($collection)
        ? asList(UniqueByOperation::of($collection)($callback))
        : asArray(UniqueByOperation::of($collection)($callback));
}
