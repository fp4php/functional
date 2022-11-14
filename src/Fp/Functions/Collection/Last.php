<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\LastOperation;

use function Fp\Callable\dropFirstArg;

/**
 * Returns last collection element
 * and None if there is no last element
 *
 * ```php
 * >>> last([1, 2, 3])->get()
 * => 3
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function last(iterable $collection, ?callable $predicate = null): Option
{
    return LastOperation::of($collection)(null !== $predicate ? dropFirstArg($predicate) : null);
}

/**
 * Same as {@see last()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 * @return Option<TV>
 */
function lastKV(iterable $collection, callable $predicate): Option
{
    return LastOperation::of($collection)($predicate);
}
