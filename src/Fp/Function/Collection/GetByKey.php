<?php

declare(strict_types=1);

namespace Fp\Function\Collection;

use ArrayAccess;
use Fp\Functional\Option\Option;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param array<TK, TV>|ArrayAccess<TK, TV> $collection
 * @psalm-param TK $key
 *
 * @psalm-return Option<TV>
 */
function getByKey(array|ArrayAccess $collection, int|string $key): Option
{
    return Option::of($collection[$key] ?? null);
}

