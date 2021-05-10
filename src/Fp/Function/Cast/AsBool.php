<?php

declare(strict_types=1);

namespace Fp\Function\Cast;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<bool>
 */
function asBool(mixed $potential): Option
{
    return Option::of(filter_var($potential, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
}
