<?php

declare(strict_types=1);

namespace Fp\Reflection;

use Fp\Functional\Either\Either;
use ReflectionException;
use ReflectionProperty;

/**
 * Returns property reflection or Left on exception
 *
 * @template T of object
 *
 * @psalm-param T|class-string<T> $class
 *
 * @psalm-return Either<ReflectionException, ReflectionProperty>
 */
function getReflectionProperty(object|string $class, string $property): Either
{
    return Either::try(fn() => new ReflectionProperty($class, $property));
}
