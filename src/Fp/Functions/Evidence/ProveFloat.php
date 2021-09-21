<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of float type
 *
 * ```php
 * >>> proveFloat(1);
 * => None
 *
 * >>> proveFloat(1.1);
 * => Some(1.1)
 * ```
 *
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<float>
 * @psalm-pure
 */
function proveFloat(mixed $potential): Option
{
    return Option::fromNullable(is_float($potential) ? $potential : null);
}
