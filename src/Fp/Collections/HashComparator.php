<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveOf;

/**
 * @internal
 */
final class HashComparator
{
    public static function hashEquals(mixed $lhs, mixed $rhs): bool
    {
        return self::asHashContract($lhs)->map(fn(HashContract $lhs) => $lhs->equals($rhs))
            ->orElse(fn() => self::asHashContract($rhs)->map(fn(HashContract $rhs) => $rhs->equals($lhs)))
            ->getOrCall(fn() => self::computeHash($lhs) === self::computeHash($rhs));
    }

    public static function computeHash(mixed $subject): mixed
    {
        return match (true) {
            is_object($subject) => self::computeHashForObject($subject),
            is_array($subject) => self::computeHashForArray($subject),
            default => $subject,
        };
    }

    public static function computeHashForObject(object $object): string
    {
        return self::asHashContract($object)
            ->map(fn(HashContract $hc) => $hc->hashCode())
            ->getOrCall(fn() => spl_object_hash($object));
    }

    public static function computeHashForArray(array $arr): string
    {
        $list = LinkedList::collect($arr)
            ->map(fn($elem): mixed => self::computeHash($elem))
            ->toList();

        return json_encode($list) ?: '';
    }

    /**
     * @return Option<HashContract>
     */
    private static function asHashContract(mixed $value): Option
    {
        return proveOf($value, HashContract::class)->orElse(fn() => HashContractGlobal::get($value));
    }
}
