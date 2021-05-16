<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of class-string type
 *
 * @psalm-return Option<class-string>
 */
function proveClassString(string $potential): Option
{
    return Option::of(class_exists($potential) ? $potential : null);
}
