<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Option;

use Exception;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

final class OptionTest extends TestCase
{
    public function testCreation(): void
    {
        $this->assertInstanceOf(Some::class, Option::fromNullable(1));
        $this->assertEquals(1, Option::fromNullable(1)->get());
        $this->assertInstanceOf(None::class, Option::fromNullable(null));
        $this->assertNull(Option::fromNullable(null)->get());

        $this->assertInstanceOf(Some::class, Option::some(1));
        $this->assertEquals(1, Option::some(1)->get());
        $this->assertInstanceOf(None::class, Option::none());
        $this->assertNull(Option::none()->get());
    }

    public function testIsMethods(): void
    {
        $this->assertFalse(Option::some(1)->isEmpty());
        $this->assertTrue(Option::some(1)->isNonEmpty());

        $this->assertFalse(Option::some(1)->isNone());
        $this->assertTrue(Option::some(1)->isSome());
    }

    public function testMap(): void
    {
        $some = Option::some(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $someAlso = Option::some(1)
            ->map(fn(int $s) => $s + 1)
            ->map(function(int $s) {
                /** @var int|null $n */
                $n = null;

                return $n;
            });

        $this->assertEquals(3, $some->get());
        $this->assertNull($someAlso->get());
        $this->assertInstanceOf(Some::class, $someAlso);
    }

    public function testFlatMap(): void
    {
        $some = Option::some(1)
            ->flatMap(fn(int $s) => Option::fromNullable($s + 1))
            ->flatMap(fn(int $s) => Option::fromNullable($s + 1));

        $none = Option::some(1)
            ->flatMap(fn(int $s) => Option::fromNullable($s + 1))
            ->flatMap(function(int $s) {
                /** @var int|null $n */
                $n = null;

                return Option::fromNullable($n);
            })
            ->flatMap(fn(int $s) => Option::fromNullable($s + 1));

        $this->assertEquals(3, $some->get());
        $this->assertNull($none->get());
    }

    public function testTry(): void
    {
        $this->assertInstanceOf(Some::class, Option::try(fn() => 1));
        $this->assertEquals(1, Option::try(fn() => 1)->get());

        $this->assertInstanceOf(None::class, Option::try(fn() => throw new Exception()));
        $this->assertNull(Option::try(fn() => throw new Exception())->get());
    }

    public function testFold(): void
    {
        $foldSome = Option::some(1)->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $foldNone = Option::none()->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $this->assertEquals(2, $foldSome);
        $this->assertEquals(0, $foldNone);
    }

    public function testGetOrElse(): void
    {
        $this->assertEquals(1, Option::some(1)->getOrElse(0));
        $this->assertEquals(0, Option::none()->getOrElse(0));
    }

    public function testToEither(): void
    {
        $this->assertInstanceOf(Left::class, Option::some(0)->toLeft(fn() => 1));
        $this->assertEquals(0, Option::some(0)->toLeft(fn() => 1)->get());

        $this->assertInstanceOf(Right::class, Option::none()->toLeft(fn() => 1));
        $this->assertEquals(1, Option::none()->toLeft(fn() => 1)->get());

        $this->assertInstanceOf(Right::class, Option::some(1)->toRight(fn() => 0));
        $this->assertEquals(1, Option::some(1)->toRight(fn() => 0)->get());

        $this->assertInstanceOf(Left::class, Option::none()->toRight(fn() => 0));
        $this->assertEquals(0, Option::none()->toRight(fn() => 0)->get());
    }
}
