<?php

declare(strict_types=1);

namespace Tests\Runtime\Json;

use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Json\jsonDecode;
use function Fp\Json\jsonSearch;

final class JsonTest extends TestCase
{
    public function testJsonDecode(): void
    {
        $this->assertInstanceOf(Left::class, jsonDecode(''));
        $this->assertInstanceOf(Right::class, jsonDecode('{"a": [{"b": true}]}'));
    }

    public function testJsonSearch(): void
    {
        $this->assertInstanceOf(Some::class, jsonSearch('a[0].b', ['a' => [['b' => true]]]));
        $this->assertInstanceOf(Some::class, jsonSearch('a[0].b', '{"a": [{"b": true}]}'));
        $this->assertTrue(jsonSearch('a[0].b', '{"a": [{"b": true}]}')->get());
    }
}
