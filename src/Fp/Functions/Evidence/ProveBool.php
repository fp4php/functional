<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<bool>
 */
function proveBool(mixed $potential): Option
{
    return Option::of(is_bool($potential) ? $potential : null);
}

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<true>
 */
function proveTrue(mixed $potential): Option
{
    return Option::of(is_bool($potential) && true === $potential ? $potential : null);
}

/**
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<false>
 */
function proveFalse(mixed $potential): Option
{
    return Option::of(is_bool($potential) && false === $potential ? $potential : null);
}
