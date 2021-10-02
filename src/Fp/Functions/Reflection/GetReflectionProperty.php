<?php

declare(strict_types=1);

namespace Fp\Reflection;

use Fp\Functional\Either\Either;
use ReflectionProperty;
use Throwable;

/**
 * Returns property reflection or Left on exception
 *
 * ```php
 * >>> getReflectionProperty(Foo::class, 'a');
 * => Right(ReflectionProperty(Foo::class, 'a'))
 * ```
 *
 * @template T of object
 * @psalm-param T|class-string<T> $class
 * @psalm-return Either<Throwable, ReflectionProperty>
 */
function getReflectionProperty(object|string $class, string $property): Either
{
    return Either::try(fn() => new ReflectionProperty($class, $property));
}
