<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveArray;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveList;
use function Fp\Evidence\proveNonEmptyList;
use function Fp\Evidence\proveString;

final class ProveCollectionStaticTest
{
    /**
     * @return Option<list<mixed>>
     */
    public function proveListFromMixed(mixed $value): Option
    {
        return proveList($value);
    }

    /**
     * @param array<array-key, int> $value
     * @return Option<list<int>>
     */
    public function proveListFromArrayWithUnknownKey(array $value): Option
    {
        return proveList($value);
    }

    /**
     * @return Option<list<int>>
     */
    public function proveListFromMixedByCallback(mixed $value): Option
    {
        return proveList($value, proveInt(...));
    }

    /**
     * @return Option<non-empty-list<mixed>>
     */
    public function proveNonEmptyListFromMixed(mixed $value): Option
    {
        return proveNonEmptyList($value);
    }

    /**
     * @param iterable<mixed, int> $value
     * @return Option<non-empty-list<int>>
     */
    public function proveNonEmptyListFromIterableWithUnknownKey(iterable $value): Option
    {
        return proveNonEmptyList($value);
    }

    /**
     * @return Option<non-empty-list<int>>
     */
    public function proveNonEmptyListFromMixedByCallback(mixed $value): Option
    {
        return proveNonEmptyList($value, proveInt(...));
    }

    /**
     * @return Option<array<array-key, mixed>>
     */
    public function proveArrayFromMixed(mixed $value): Option
    {
        return proveArray($value);
    }

    /**
     * @param iterable<mixed, int> $value
     * @return Option<array<array-key, int>>
     */
    public function proveArrayFromIterableWithUnknownKey(mixed $value): Option
    {
        return proveArray($value);
    }

    /**
     * @param iterable<string, mixed> $value
     * @return Option<array<string, mixed>>
     */
    public function proveArrayFromIterableWithUnknownValue(iterable $value): Option
    {
        return proveArray($value);
    }

    /**
     * @param iterable<string, int> $value
     * @return Option<array<string, int>>
     */
    public function proveArrayFromIterable(iterable $value): Option
    {
        return proveArray($value);
    }

    /**
     * @param iterable<mixed, int> $value
     * @return Option<array<string, int>>
     */
    public function proveArrayFromIterableWithUnknownKeyByCallback(iterable $value): Option
    {
        return proveArray($value, kType: proveString(...));
    }

    /**
     * @param iterable<string, mixed> $value
     * @return Option<array<string, int>>
     */
    public function proveArrayFromIterableWithUnknownValueByCallback(iterable $value): Option
    {
        return proveArray($value, vType: proveInt(...));
    }

    /**
     * @return Option<array<string, int>>
     */
    public function proveArrayFromMixedByCallback(mixed $value): Option
    {
        return proveArray($value,
            kType: proveString(...),
            vType: proveInt(...));
    }
}
