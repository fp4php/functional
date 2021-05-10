<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<float>
 */
function asFloat(mixed $potential): Option
{
    return Option::of(filter_var($potential, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE));
}
