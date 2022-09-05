<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Option;

use Exception;
use Fp\Collections\ArrayList;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class OptionTest extends TestCase
{
    public function testOptionPluck(): void
    {
        $this->assertEquals(Option::some(1), Option::some(new Foo(a: 1))->pluck('a'));
        $this->assertEquals(Option::some(1), Option::some(['a' => 1])->pluck('a'));
    }

    public function testToString(): void
    {
        $this->assertEquals('None', (string) Option::none());
        $this->assertEquals('Some(42)', (string) Option::some(42));
        $this->assertEquals('None', Option::none()->toString());
        $this->assertEquals('Some(42)', Option::some(42)->toString());
        $this->assertEquals('Some(true)', Option::some(true)->toString());
        $this->assertEquals('Some(false)', Option::some(false)->toString());
    }

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

    public function testMapN(): void
    {
        $some = Option::some([1, true, false])->mapN(Foo::create(...));
        $someAlso = Option::some([1, true])->mapN(Foo::create(...));
        $someToo = Option::some(['a' => 1, 'b' => true, 'c' => false])->mapN(Foo::create(...));

        $this->assertEquals(Option::some(new Foo(1, true, false)), $some);
        $this->assertEquals(Option::some(new Foo(1, true, true)), $someAlso);
        $this->assertEquals(Option::some(new Foo(1, true, false)), $someToo);
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

    public function testFlatMapN(): void
    {
        $some = Option::some([1, true, false])->flatMapN(Foo::createOption(...));
        $none = Option::some([0, true, false])->flatMapN(Foo::createOption(...));

        $this->assertEquals(Option::some(new Foo(1, true, false)), $some);
        $this->assertEquals(Option::none(), $none);
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
            fn() => 0,
            fn(int $some) => $some + 1,
        );

        $foldNone = Option::none()->fold(
            fn() => 0,
            fn(int $some) => $some + 1,
        );

        $this->assertEquals(2, $foldSome);
        $this->assertEquals(0, $foldNone);
    }

    public function testGetOrElse(): void
    {
        $this->assertEquals(1, Option::some(1)->getOrElse(0));
        $this->assertEquals(0, Option::none()->getOrElse(0));
    }

    public function testGetOrCall(): void
    {
        $this->assertEquals(1, Option::some(1)->getOrCall(fn() => 0));
        $this->assertEquals(0, Option::none()->getOrCall(fn() => 0));
    }

    public function testOrElse(): void
    {
        $this->assertEquals(
            1,
            Option::some(1)->orElse(fn() => Option::some(2))->get()
        );

        $this->assertEquals(
            2,
            Option::none()->orElse(fn() => Option::some(2))->get()
        );
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

    public function testFilter(): void
    {
        /** @var int $num */
        $num = 42;

        $some = Option::some($num)
            ->filter(fn(int $v) => $v >= 42)
            ->get();

        $this->assertEquals($num, $some);

        $none = Option::some($num)
            ->filter(fn(int $v) => $v > 42)
            ->get();

        $this->assertEquals(null, $none);
    }

    public function testFilterOf(): void
    {
        $this->assertInstanceOf(
            Foo::class,
            Option::some(new Foo(1))->filterOf(Foo::class)->get()
        );
        $this->assertNull(
            Option::some(new Bar(1))->filterOf(Foo::class)->get()
        );
    }

    public function filterOfWithMultipleFqcnDataProvider(): iterable
    {
        yield [new Foo(1)];
        yield [new Bar(2)];
    }

    /**
     * @dataProvider filterOfWithMultipleFqcnDataProvider
     */
    public function testFilterOfWithMultipleFqcn(Foo|Bar $item): void
    {
        $this->assertEquals(
            Option::some($item),
            Option::some($item)->filterOf([Foo::class, Bar::class]),
        );
    }

    public function testTap(): void
    {
        $this->assertEquals(1, Option::some(1)->tap(fn($e) => $e)->get());
        $this->assertNull(Option::none()->tap(fn($e) => $e)->get());
    }

    public function testTapN(): void
    {
        $some = Option::some([1, true, false])
            ->tapN(function(int $a, bool $b, bool $c) {
                $this->assertEquals(1, $a);
                $this->assertEquals(true, $b);
                $this->assertEquals(false, $c);
            });

        $this->assertEquals(Option::some([1, true, false]), $some);
    }

    public function testFlatTap(): void
    {
        $this->assertEquals(1, Option::some(1)->flatTap(fn($e) => Option::some(2))->get());
        $this->assertNull(Option::some(1)->flatTap(fn($e) => Option::none())->get());
    }

    public function testFlatTapN(): void
    {
        $some = Option::some([1, true, false])->flatTapN(Foo::createOption(...));
        $none = Option::some([0, true, false])->flatTapN(Foo::createOption(...));

        $this->assertEquals(Option::some([1, true, false]), $some);
        $this->assertEquals(Option::none(), $none);
    }

    public function testToArrayList(): void
    {
        $this->assertEquals(ArrayList::collect([1]), Option::some(1)->toArrayList());
        $this->assertEquals(ArrayList::collect([]), Option::none()->toArrayList());
    }
}
