<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * Try cast float like value
 * Returns None if cast is not possible
 *
 * ```php
 * >>> asFloat('1.1');
 * => Some(1.1)
 *
 * >>> asFloat('1zxc1');
 * => None
 * ```
 *
 * @psalm-template T
 * @psalm-param T $subject
 * @psalm-return Option<float>
 */
function asFloat(mixed $subject): Option
{
    return Option::fromNullable(filter_var($subject, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE));
}
