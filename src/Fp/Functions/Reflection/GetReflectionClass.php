<?php

declare(strict_types=1);

namespace Fp\Reflection;

use Fp\Functional\Either\Either;
use ReflectionClass;
use Throwable;

/**
 * Returns class reflection or Left on exception
 *
 * ```php
 * >>> getReflectionClass(Foo::class);
 * => Right(ReflectionClass(Foo::class))
 * ```
 *
 * @template T of object
 *
 * @param T|class-string<T> $objectOrClass
 * @return Either<Throwable, ReflectionClass>
 */
function getReflectionClass(object|string $objectOrClass): Either
{
    return Either::try(fn() => new ReflectionClass($objectOrClass));
}
