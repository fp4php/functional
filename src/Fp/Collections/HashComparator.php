<?php

declare(strict_types=1);

namespace Fp\Collections;

use UnitEnum;
use BackedEnum;
use Fp\Functional\Option\Option;

use function Fp\Util\jsonEncode;
use function Fp\Collection\map;
use function Fp\Evidence\proveOf;

/**
 * @internal
 */
final class HashComparator
{
    public static function hashEquals(mixed $lhs, mixed $rhs): bool
    {
        return Option
            ::firstT(
                fn() => self::asHashContract($lhs)->map(fn(HashContract $lhs) => $lhs->equals($rhs)),
                fn() => self::asHashContract($rhs)->map(fn(HashContract $rhs) => $rhs->equals($lhs)),
            )
            ->getOrCall(fn() => self::computeHash($lhs) === self::computeHash($rhs));
    }

    public static function computeHash(mixed $subject): mixed
    {
        return match (true) {
            is_object($subject) => self::asHashContract($subject)
                ->map(fn(HashContract $hc) => $hc->hashCode())
                ->getOrCall(fn() => spl_object_hash($subject)),
            is_array($subject) => jsonEncode(
                map($subject, self::computeHash(...)),
            ),
            $subject instanceof BackedEnum => $subject->value,
            $subject instanceof UnitEnum => $subject->name,
            default => $subject,
        };
    }

    /**
     * @return Option<HashContract>
     */
    private static function asHashContract(mixed $value): Option
    {
        return Option::firstT(
            fn() => proveOf($value, HashContract::class),
            fn() => HashContractGlobal::get($value),
        );
    }
}
