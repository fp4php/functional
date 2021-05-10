<?php

declare(strict_types=1);

namespace Tests\Runtime;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\at;
use function Fp\Collection\copyCollection;
use function Fp\Collection\every;
use function Fp\Collection\filter;

final class CollectionFunctionTest extends TestCase
{
    public function testAt(): void
    {
        $this->assertTrue(at(['a' => true], 'a')->get());
    }

    public function testCopyCollection(): void
    {
        $collection = ['a' => 1, 'b' => 2];
        $this->assertEquals($collection, copyCollection($collection));
    }

    public function testEvery(): void
    {
        $collection = [1, 2];

        $this->assertTrue(every(
            $collection,
            fn(int $v) => $v < 3
        ));

        $this->assertFalse(every(
            $collection,
            fn(int $v) => $v < 2
        ));
    }

    public function testFilter(): void
    {
        $collection = [1, 2];

        $this->assertEquals([1], filter(
            $collection,
            fn(int $v) => $v < 2
        ));
    }
}
