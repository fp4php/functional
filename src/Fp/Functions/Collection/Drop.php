<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\DropOperation;
use Fp\Operations\DropRightOperation;
use Fp\Operations\DropWhileOperation;

use function Fp\Cast\asList;

/**
 * Drop N collection elements
 *
 * ```php
 * >>> drop([1, 2, 3], 2);
 * => [3]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @return list<TV>
 */
function drop(iterable $collection, int $length): array
{
    return asList(DropOperation::of($collection)($length));
}

/**
 * Drop N collection elements from the end
 *
 * ```php
 * >>> drop([1, 2, 3], 1);
 * => [1, 2]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @return list<TV>
 */
function dropRight(iterable $collection, int $length): array
{
    return DropRightOperation::of($collection)($length);
}

/**
 * Drop collection elements while predicate is true
 *
 * ```php
 * >>> dropWhile([1, 2, 3, 4, 5], fn($e) => $e < 3);
 * => [4, 5]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param callable(TV): bool $predicate
 * @return list<TV>
 */
function dropWhile(iterable $collection, callable $predicate): array
{
    return asList(DropWhileOperation::of($collection)($predicate));
}
