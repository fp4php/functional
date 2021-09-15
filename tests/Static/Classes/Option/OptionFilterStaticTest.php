<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;

/**
 * @todo check why it's not working as method
 * @see OptionFilterStaticTest::testRefineShapeWithPsalmAssert
 * @psalm-type Shape = array{name: string, postcode: int}
 * @psalm-assert-if-true Shape $shape
 */
function isValidShape(array $shape): bool
{
    return array_key_exists("name", $shape) &&
        array_key_exists("postcode", $shape) &&
        is_int($shape["postcode"]);
}

final class OptionFilterStaticTest
{
    /**
     * @param array $in
     * @return Option<array{a: mixed}>
     */
    public function testPreviousTypeRemainUnchanged(array $in): Option
    {
        $withA = Option::fromNullable($in)
            ->filter(fn($arr) => array_key_exists('a', $arr));

        $withAB = $withA
            ->filter(fn($arr) => array_key_exists('b', $arr));

        return $withA;
    }

    /**
     * @param Option<null|int> $in
     * @return Option<int>
     */
    public function testRefineNotNull(Option $in): Option
    {
        return $in->filter(fn(null|int $v) => null !== $v);
    }

    /**
     * @psalm-type Shape = array{name?: string, postcode?: int|string}
     * @psalm-param Option<Shape> $in
     * @return Option<array{name: string, postcode: int}>
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function testRefineShapeType(Option $in): Option
    {
        return $in->filter(
            fn(array $v) =>
                array_key_exists("name", $v) &&
                array_key_exists("postcode", $v) &&
                is_int($v["postcode"])
        );
    }

    /**
     * @psalm-param Option<array> $in
     * @return Option<array{name: string, postcode: int}>
     */
    public function testRefineShapeWithPsalmAssert(Option $in): Option
    {
        return $in->filter(
            fn(array $v) => isValidShape($v)
        );
    }
}
