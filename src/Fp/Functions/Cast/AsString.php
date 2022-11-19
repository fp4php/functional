<?php

declare(strict_types=1);

namespace Fp\Cast;

use Stringable;
use Fp\Functional\Option\Option;

/**
 * Try cast value to string
 * Returns None if cast is not possible
 *
 * ```php
 * >>> asString('1');
 * => Some('1')
 *
 * >>> asString(1);
 * => Some('1')
 *
 * >>> asString(...any Stringable object...)
 * >>> Some(...result of __toString call...)
 * ```
 *
 * @return Option<string>
 */
function asString(mixed $subject): Option
{
    return Option::fromNullable(match (true) {
        is_string($subject) => $subject,
        is_int($subject), is_float($subject) => "{$subject}",
        is_bool($subject) => $subject ? 'true' : 'false',
        $subject instanceof Stringable => (string) $subject,
        default => null,
    });
}
