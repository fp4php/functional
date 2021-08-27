<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Json;

use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use PHPUnit\Framework\TestCase;

use function Fp\Json\jsonDecode;

final class JsonTest extends TestCase
{
    public function testJsonDecode(): void
    {
        $this->assertInstanceOf(Left::class, jsonDecode(''));
        $this->assertInstanceOf(Right::class, jsonDecode('{"a": [{"b": true}]}'));
        $this->assertEquals(1, jsonDecode('1')->get());
        $this->assertEquals("1", jsonDecode('"1"')->get());
        $this->assertEquals(true, jsonDecode('true')->get());
    }
}
