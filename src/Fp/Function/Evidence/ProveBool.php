<?php

declare(strict_types=1);

namespace Fp\Function\Evidence;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<bool>
 */
function proveBool(mixed $potential): Option
{
    return Option::of(is_bool($potential) ? $potential : null);
}
