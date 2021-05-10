<?php

declare(strict_types=1);

namespace Fp\Function\Evidence;

use Fp\Functional\Option\Option;

/**
 * @psalm-return Option<callable-string>
 */
function proveCallableString(string $potential): Option
{
    return Option::of(is_callable($potential) ? $potential : null);
}
