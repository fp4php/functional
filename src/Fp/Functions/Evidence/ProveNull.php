<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is null.
 *
 * ```php
 * >>> proveNull(null);
 * => Some(null)
 *
 * >>> proveNull(1);
 * => None
 * ```
 *
 * @return Option<null>
 */
function proveNull(mixed $potential): Option
{
    return null === $potential ? Option::some($potential) : Option::none();
}
