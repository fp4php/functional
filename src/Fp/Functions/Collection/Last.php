<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\LastOperation;

/**
 * Returns last collection element
 * and None if there is no last element
 *
 * ```php
 * >>> last([1, 2, 3])->get()
 * => 3
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function last(iterable $collection, ?callable $predicate = null): Option
{
    return LastOperation::of($collection)($predicate);
}
