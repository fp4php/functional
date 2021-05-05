<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template T
 *
 * @psalm-param T $value
 *
 * @psalm-return T
 */
function id(mixed $value): mixed
{
    return $value;
}
