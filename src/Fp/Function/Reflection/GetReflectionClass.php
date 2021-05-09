<?php

declare(strict_types=1);

namespace Fp\Function\Reflection;

use Fp\Functional\Either\Either;
use ReflectionClass;
use ReflectionException;

/**
 * @template T of object
 * @psalm-param T|class-string<T> $objectOrClass
 * @psalm-return Either<ReflectionException, ReflectionClass>
 */
function getReflectionClass(object|string $objectOrClass): Either
{
    return Either::try(fn() => new ReflectionClass($objectOrClass));
}
