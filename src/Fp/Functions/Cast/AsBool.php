<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * Try cast boolean like value
 * Returns None if cast is not possible
 *
 * REPL:
 * >>> asBool('yes');
 * => Option<bool>
 *
 * @psalm-template T
 * @psalm-param T $subject
 * @psalm-return Option<bool>
 */
function asBool(mixed $subject): Option
{
    return Option::fromNullable(filter_var($subject, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
}
