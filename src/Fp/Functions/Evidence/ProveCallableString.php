<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of callable-string type
 *
 * @psalm-return Option<callable-string>
 */
function proveCallableString(string $subject): Option
{
    return Option::of(is_callable($subject) ? $subject : null);
}
