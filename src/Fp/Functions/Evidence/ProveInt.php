<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<int>
 */
function proveInt(mixed $potential): Option
{
    return Option::of(is_int($potential) ? $potential : null);
}
