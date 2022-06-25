<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\filter;

/**
 * @todo
 * @psalm-type Shape = array{name: string, postcode: int}
 * @psalm-type ShapeWithPossiblyUndefinedPostcode = array{name?: string, postcode?: int|string}
 *
 * @psalm-assert-if-true Shape $shape
 */
function isValidShape(array $shape): bool
{
    return array_key_exists("name", $shape) &&
        array_key_exists("postcode", $shape) &&
        is_int($shape["postcode"]);
}

final class FilterStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return array<string, int>
     */
    public function testPreserveKeysTrue(array $coll): array
    {
        return filter(
            $coll,
            fn(int $v) => true,
            preserveKeys: true
        );
    }

    /**
     * @param array<string, int> $coll
     * @return list<int>
     */
    public function testPreserveKeysExplicitFalse(array $coll): array
    {
        return filter(
            $coll,
            fn(int $v) => true,
            preserveKeys: false
        );
    }

    /**
     * @param array<string, int> $coll
     * @return list<int>
     */
    public function testPreserveKeysImplicitFalse(array $coll): array
    {
        return filter(
            $coll,
            fn(int $v) => true,
        );
    }

    /**
     * @param array<string, int> $coll
     * @return array<string, int>
     */
    public function testPreserveKeysIsNonLiteralBool(array $coll): array
    {
        return filter(
            $coll,
            fn(int $v) => true,
            preserveKeys: (bool) rand(0, 1)
        );
    }

    /**
     * @param array<string, int> $coll
     * @return list<int>
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function testRefineNotNull(array $coll): array
    {
        return filter(
            $coll,
            fn(null|int $v) => null !== $v
        );
    }

    /**
     * @param array<string, ShapeWithPossiblyUndefinedPostcode> $coll
     * @return list<array{name: string, postcode: int}>
     */
    public function testRefineShapeType(array $coll): array
    {
        return filter(
            $coll,
            fn(array $v) =>
                array_key_exists("name", $v) &&
                array_key_exists("postcode", $v) &&
                is_int($v["postcode"])
        );
    }

    /**
     * @param array<string, array>  $coll
     * @return list<array{name: string, postcode: int}>
     */
    public function testRefineShapeWithPsalmAssert(array $coll): array
    {
        return filter(
            $coll,
            fn(array $v) => isValidShape($v)
        );
    }
}
