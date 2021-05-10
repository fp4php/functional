<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<string>
 */
function proveString(mixed $potential): Option
{
    return Option::of(is_string($potential) ? $potential : null);
}
