<?php

declare(strict_types=1);

namespace Fp\Callable;

use Closure;

/**
 * @template A
 *
 * @param class-string<A> $class
 * @return Closure(mixed...): A
 */
function ctor(string $class): Closure
{
    /** @psalm-suppress MixedMethodCall */
    return fn(mixed ...$args) => new $class(...$args);
}
