<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @internal
 */
final class HashComparator
{
    public static function hashEquals(mixed $lhs, mixed $rhs): bool
    {
        return match (true) {
            $lhs instanceof HashContract => $lhs->equals($rhs),
            $rhs instanceof HashContract => $rhs->equals($lhs),
            default => self::computeHash($lhs) === self::computeHash($rhs),
        };
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
        return $object instanceof HashContract
            ? $object->hashCode()
            : spl_object_hash($object);
    }

    public static function computeHashForArray(array $arr): string
    {
        $list = LinkedList::collect($arr)
            ->map(fn($elem): mixed => self::computeHash($elem))
            ->toList();

        return json_encode($list) ?: '';
    }
}
