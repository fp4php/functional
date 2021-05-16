<?php

declare(strict_types=1);

namespace Fp;

const id = '\Fp\id';

/**
 * Identity function
 *
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
