<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
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
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function first(iterable $collection, ?callable $predicate = null): Option
{
    return FirstOperation::of($collection)(null !== $predicate ? dropFirstArg($predicate) : null);
}

/**
 * Same as {@see first()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 * @return Option<TV>
 */
function firstKV(iterable $collection, callable $predicate): Option
{
    return FirstOperation::of($collection)($predicate);
}
