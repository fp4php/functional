<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * Try cast boolean like value
 * Returns None if cast is not possible
 *
 * ```php
 * >>> asBool('yes');
 * => Some(true);
 *
 * >>> asBool('no');
 * => Some(false)
 *
 * >>> asBool('xzc');
 * => None
 * ```
 *
 * @return Option<bool>
 */
function asBool(mixed $subject): Option
{
    return Option::fromNullable(filter_var($subject, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
}
