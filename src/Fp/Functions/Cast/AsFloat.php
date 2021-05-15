<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * Try cast float like value
 * Returns None if cast is not possible
 *
 * @psalm-template T
 *
 * @psalm-param T $potential
 *
 * @psalm-return Option<float>
 */
function asFloat(mixed $potential): Option
{
    return Option::of(filter_var($potential, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE));
}
