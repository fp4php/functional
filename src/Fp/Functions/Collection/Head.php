<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\HeadOperation;

/**
 * Returns collection first element
 *
 * ```php
 * >>> head([1, 2, 3])->get();
 * => 1
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return Option<TV>
 */
function head(iterable $collection): Option
{
    return HeadOperation::of($collection)();
}
