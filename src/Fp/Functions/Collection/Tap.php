<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\TapOperation;
use Fp\Streams\Stream;

use function Fp\Callable\dropFirstArg;

/**
 * Do something for all collection elements
 *
 * ```php
 * >>> tap([1, 2, 3], function($v) { echo($v); });
 * => 123
 * ```
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): void $callback
 * @return iterable<TK, TV>
 * @psalm-return (
 *    $collection is non-empty-list<TV>      ? non-empty-list<TV>      :
 *    $collection is list<TV>                ? list<TV>                :
 *    $collection is non-empty-array<TK, TV> ? non-empty-array<TK, TV> :
 *    iterable<TK, TV>
 * )
 */
function tap(iterable $collection, callable $callback): iterable
{
    tapKV($collection, dropFirstArg($callback));
    return $collection;
}

/**
 * Same as {@see tap()} but passing also the key to the $callback function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): void $callback
 * @return iterable<TK, TV>
 * @psalm-return (
 *    $collection is non-empty-list<TV>      ? non-empty-list<TV>      :
 *    $collection is list<TV>                ? list<TV>                :
 *    $collection is non-empty-array<TK, TV> ? non-empty-array<TK, TV> :
 *    iterable<TK, TV>
 * )
 */
function tapKV(iterable $collection, callable $callback): iterable
{
    Stream::emits(TapOperation::of($collection)($callback))->drain();
    return $collection;
}
