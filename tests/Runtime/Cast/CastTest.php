<?php

declare(strict_types=1);

namespace Tests\Runtime\Cast;

use Fp\Functional\Option\None;
use PHPUnit\Framework\TestCase;

use function Fp\Cast\asArray;
use function Fp\Cast\asBool;
use function Fp\Cast\asFloat;
use function Fp\Cast\asInt;
use function Fp\Cast\asList;
use function Fp\Cast\asNonEmptyArray;
use function Fp\Cast\asNonEmptyList;

final class CastTest extends TestCase
{
    public function testAsArray(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals($c, asArray($c));
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
}
