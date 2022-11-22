<?php

namespace Fp\Cast;

use Closure;
use BackedEnum;
use Fp\Functional\Option\Option;

/**
 * Cast string or int to BackedEnum instance.
 *
 * ```php
 * >>> asEnumOf(1, IntEnum::class)
 * => Some(IntEnum(1))
 * >>> asEnumOf(42, IntEnum::class)
 * => None
 * ```
 *
 * @template TEnum of BackedEnum
 *
 * @param class-string<TEnum> $enum
 * @return Option<TEnum>
 */
function asEnumOf(mixed $subject, string $enum): Option
{
    if (!is_string($subject) && !is_int($subject)) {
        return Option::none();
    }

    return Option::try(fn() => $enum::from($subject));
}

/**
 * Curried version of {@see asEnumOf}.
 *
 * ```php
 * >>> asEnumOf(IntEnum::class)(1)
 * => Some(IntEnum(1))
 * >>> asEnumOf(IntEnum::class)(42)
 * => None
 * ```
 *
 * @template TEnum of BackedEnum
 *
 * @param class-string<TEnum> $enum
 * @return Closure(mixed): Option<TEnum>
 */
function enumOf(string $enum): Closure
{
    return fn(mixed $subject) => asEnumOf($subject, $enum);
}
