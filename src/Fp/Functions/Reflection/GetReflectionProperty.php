<?php

declare(strict_types=1);

namespace Fp\Reflection;

use Fp\Functional\Either\Either;
use ReflectionException;
use ReflectionProperty;
use Throwable;

/**
 * Returns property reflection or Left on exception
 *
 * REPL:
 * >>> getReflectionProperty(Foo::class, 'a');
 * => Either<Throwable, ReflectionProperty>
 *
 *
 * @template T of object
 *
 * @psalm-param T|class-string<T> $class
 *
 * @psalm-return Either<Throwable, ReflectionProperty>
 */
function getReflectionProperty(object|string $class, string $property): Either
{
    return Either::try(fn() => new ReflectionProperty($class, $property));
}
