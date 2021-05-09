<?php

declare(strict_types=1);

namespace Fp\Function\Cast;

use Fp\Functional\Option\Option;

/**
 * @psalm-return Option<callable-string>
 */
function asCallableString(string $potential): Option
{
    return Option::of(is_callable($potential) ? $potential : null);
}
