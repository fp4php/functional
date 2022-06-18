<?php

declare(strict_types=1);

namespace Fp;

const id = '\Fp\id';

/**
 * Identity function
 *
 * ```php
 * >>> id(1);
 * => 1
 * ```
 *
 * @template T
 *
 * @param T $value
 * @return T
 */
function id(mixed $value): mixed
{
    return $value;
}
