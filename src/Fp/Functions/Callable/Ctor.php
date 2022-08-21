<?php

declare(strict_types=1);

namespace Fp\Callable;

use Closure;
use Fp\Psalm\Hook\FunctionReturnTypeProvider\CtorFunctionReturnTypeProvider;

/**
 * Makes class constructor from fqcn
 *
 * >>> ctor(Foo::class)
 * => Closure(int $a, bool $b = true, bool $c = true): Foo
 *
 * @template A
 *
 * @param class-string<A> $class
 * @return Closure(mixed...): A
 *
 * @see CtorFunctionReturnTypeProvider
 */
function ctor(string $class): Closure
{
    /** @psalm-suppress MixedMethodCall */
    return fn(mixed ...$args) => new $class(...$args);
}
