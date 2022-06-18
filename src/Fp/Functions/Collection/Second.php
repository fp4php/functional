<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\SecondOperation;

/**
 * Returns second collection element
 * None if there is no second collection element
 *
 * ```php
 * >>> second([1, 2, 3])->get();
 * => 2
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param null|callable(TV, TK): bool $predicate
 *
 * @return Option<TV>
 */
function second(iterable $collection, ?callable $predicate = null): Option
{
    return SecondOperation::of($collection)($predicate);
}
