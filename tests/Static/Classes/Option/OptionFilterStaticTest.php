<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;

/**
 * @psalm-type Shape = array{name: string, postcode: int}
 * @psalm-type RawShape = array{name?: string, postcode?: int|string}
 */
final class OptionFilterStaticTest
{
    /**
     * @param array $in
     * @return Option<array{a: mixed, b: mixed, ...}>
     */
    public function testPreviousTypeRemainUnchanged(array $in): Option
    {
        return Option::fromNullable($in)
            ->filter(fn($arr) => array_key_exists('a', $arr))
            ->filter(fn($arr) => array_key_exists('b', $arr));
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
     * @param Option<RawShape> $in
     * @return Option<Shape>
     */
    public function testRefineShapeType(Option $in): Option
    {
        return $in->filter(
            fn(array $v) =>
                array_key_exists('name', $v) &&
                array_key_exists('postcode', $v) &&
                is_int($v['postcode'])
        );
    }

    /**
     * @param Option<array> $in
     * @return Option<array{name: string, postcode: int}>
     */
    public function testRefineShapeWithPsalmAssert(Option $in): Option
    {
        return $in->filter(fn(array $v) => $this->isValidShape($v));
    }

    /**
     * @param Option<int|string> $in
     * @return Option<int>
     */
    public function testRefineWithFirstClassCallable(Option $in): Option
    {
        return $in->filter(is_int(...));
    }

    /**
     * @psalm-assert-if-true Shape $shape
     */
    public function isValidShape(array $shape): bool
    {
        return array_key_exists('name', $shape) &&
            array_key_exists('postcode', $shape) &&
            is_int($shape['postcode']);
    }
}
