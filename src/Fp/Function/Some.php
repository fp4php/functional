<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param \Closure(TV, TK): bool $callback
 *
 * @psalm-return bool
 */
function some(iterable $collection, \Closure $callback): bool
{
    return !(first($collection, $callback)->isEmpty());
}
