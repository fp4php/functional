<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Cast;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Baz;
use Tests\Mock\Foo;

use Tests\Mock\IntEnum;
use Tests\Mock\StringEnum;
use function Fp\Cast\asEnumOf;
use function Fp\Cast\asGenerator;
use function Fp\Cast\asArray;
use function Fp\Cast\asBool;
use function Fp\Cast\asFloat;
use function Fp\Cast\asInt;
use function Fp\Cast\asList;
use function Fp\Cast\asNonEmptyArray;
use function Fp\Cast\asNonEmptyList;
use function Fp\Cast\asPairs;
use function Fp\Cast\asPairsGenerator;
use function Fp\Cast\asString;
use function Fp\Cast\enumOf;
use function Fp\Cast\fromPairs;

final class CastTest extends TestCase
{
    public function testAsArray(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals($c, asArray($c));
    }

    public function testAsEnumOf(): void
    {
        $this->assertEquals(Option::none(), asEnumOf(0, IntEnum::class));
        $this->assertEquals(Option::none(), asEnumOf('fst', IntEnum::class));
        $this->assertEquals(Option::none(), asEnumOf(0.42, IntEnum::class));
        $this->assertEquals(Option::none(), asEnumOf([], IntEnum::class));
        $this->assertEquals(Option::some(IntEnum::FST), asEnumOf(1, IntEnum::class));
        $this->assertEquals(Option::some(IntEnum::SND), asEnumOf(2, IntEnum::class));
        $this->assertEquals(Option::some(IntEnum::THR), asEnumOf(3, IntEnum::class));

        $this->assertEquals(Option::none(), asEnumOf(0, StringEnum::class));
        $this->assertEquals(Option::none(), asEnumOf(0.42, StringEnum::class));
        $this->assertEquals(Option::none(), asEnumOf([], StringEnum::class));
        $this->assertEquals(Option::none(), asEnumOf('fth', StringEnum::class));
        $this->assertEquals(Option::some(StringEnum::FST), asEnumOf('fst', StringEnum::class));
        $this->assertEquals(Option::some(StringEnum::SND), asEnumOf('snd', StringEnum::class));
        $this->assertEquals(Option::some(StringEnum::THR), asEnumOf('thr', StringEnum::class));
    }

    public function testEnumOf(): void
    {
        $this->assertEquals(Option::none(), enumOf(IntEnum::class)(0));
        $this->assertEquals(Option::none(), enumOf(IntEnum::class)('fst'));
        $this->assertEquals(Option::none(), enumOf(IntEnum::class)(0.42));
        $this->assertEquals(Option::none(), enumOf(IntEnum::class)([]));
        $this->assertEquals(Option::some(IntEnum::FST), enumOf(IntEnum::class)(1));
        $this->assertEquals(Option::some(IntEnum::SND), enumOf(IntEnum::class)(2));
        $this->assertEquals(Option::some(IntEnum::THR), enumOf(IntEnum::class)(3));

        $this->assertEquals(Option::none(), enumOf(StringEnum::class)(0));
        $this->assertEquals(Option::none(), enumOf(StringEnum::class)(0.42));
        $this->assertEquals(Option::none(), enumOf(StringEnum::class)([]));
        $this->assertEquals(Option::none(), enumOf(StringEnum::class)('fth'));
        $this->assertEquals(Option::some(StringEnum::FST), enumOf(StringEnum::class)('fst'));
        $this->assertEquals(Option::some(StringEnum::SND), enumOf(StringEnum::class)('snd'));
        $this->assertEquals(Option::some(StringEnum::THR), enumOf(StringEnum::class)('thr'));
    }

    public function testAsBool(): void
    {
        $this->assertTrue(asBool('true')->get());
        $this->assertFalse(asBool('false')->get());
        $this->assertTrue(asBool('yes')->get());
        $this->assertFalse(asBool('no')->get());
        $this->assertTrue(asBool('1')->get());
        $this->assertFalse(asBool('0')->get());
        $this->assertInstanceOf(None::class, asBool('test'));
    }

    public function testAsFloat(): void
    {
        $this->assertIsFloat(asFloat('1')->get());
        $this->assertIsFloat(asFloat('1.001')->get());
        $this->assertInstanceOf(None::class, asFloat('1.x1'));
    }

    public function testAsInt(): void
    {
        $this->assertIsInt(asInt('1')->get());
        $this->assertInstanceOf(None::class, asInt('1.001'));
        $this->assertInstanceOf(None::class, asInt('1.x1'));
    }

    public function testAsList(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals([0, 1, 2], array_keys(asList($c)));
    }

    public function testAsNonEmptyArray(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals($c, asNonEmptyArray($c)->get() ?? []);
        $this->assertInstanceOf(None::class, asNonEmptyArray([]));
    }

    public function testAsNonEmptyList(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals([0, 1, 2], array_keys(asNonEmptyList($c)->get() ?? []));
        $this->assertInstanceOf(None::class, asNonEmptyList([]));
    }

    public function testAsGenerator(): void
    {
        $this->assertEquals([1, 2], asList(asGenerator(function() {
            yield 1;
            yield 2;
        })));

        $this->assertEquals([1, 2], asList(asGenerator(fn() => [1, 2])));
    }

    public function testAsPairs(): void
    {
        $this->assertEquals([['a', 1], ['b', 2]], asList(asPairsGenerator(['a' => 1, 'b' => 2])));
        $this->assertEquals([['a', 1], ['b', 2]], asPairs(['a' => 1, 'b' => 2]));
        $this->assertEquals(['a' => 1, 'b' => 2], fromPairs([['a', 1], ['b', 2]]));
    }

    public function testAsString(): void
    {
        $this->assertEquals('1', asString('1')->get());
        $this->assertEquals('1', asString(1)->get());
        $this->assertEquals('Baz()', asString(new Baz())->get());
        $this->assertEquals(null, asString(new Foo(a: 42))->get());
    }
}
