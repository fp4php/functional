<?php

declare(strict_types=1);

namespace Fp\Function\Reflection;

use ReflectionNamedType;
use ReflectionProperty;

function getNamedType(ReflectionProperty $property): ReflectionNamedType
{
    /** @var ReflectionNamedType $type */
    $type = $property->getType();

    return $type;
}
