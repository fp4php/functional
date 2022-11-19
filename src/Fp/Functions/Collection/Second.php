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
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param null|callable(TV): bool $predicate
 *
 * @return Option<TV>
 */
function second(iterable $collection, ?callable $predicate = null): Option
{
    return SecondOperation::of($collection)($predicate);
}
