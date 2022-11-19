<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\DropOperation;
use Fp\Operations\DropWhileOperation;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;
use function Fp\Cast\asList;

/**
 * Drop N collection elements
 *
 * ```php
 * >>> drop([1, 2, 3], 2);
 * => [3]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return array<TK, TV>
 * @psalm-return ($collection is list<TV> ? list<TV> : array<TK, TV>)
 */
function drop(iterable $collection, int $length): array
{
    $gen = DropOperation::of($collection)($length);

    return is_array($collection) && array_is_list($collection)
        ? asList($gen)
        : asArray($gen);
}

/**
 * Drop N collection elements from the end
 *
 * ```php
 * >>> drop([1, 2, 3], 1);
 * => [1, 2]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return array<TK, TV>
 * @psalm-return ($collection is list<TV> ? list<TV> : array<TK, TV>)
 */
function dropRight(iterable $collection, int $length): array
{
    return reverse(drop(reverse($collection), $length));
}

/**
 * Drop collection elements while predicate is true
 *
 * ```php
 * >>> dropWhile([1, 2, 3, 4, 5], fn($e) => $e < 3);
 * => [4, 5]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): bool $predicate
 * @return array<TK, TV>
 * @psalm-return ($collection is list<TV> ? list<TV> : array<TK, TV>)
 */
function dropWhile(iterable $collection, callable $predicate): array
{
    $gen = DropWhileOperation::of($collection)(dropFirstArg($predicate));

    return is_array($collection) && array_is_list($collection)
        ? asList($gen)
        : asArray($gen);
}
