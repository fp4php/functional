<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\FooIterable;

use function Fp\Collection\at;

final class AtTest extends TestCase
{
    public function testAtWithArray(): void
    {
        $this->assertTrue(at(['a' => true], 'a')->get());
        $this->assertNull(at(['a' => true], 'b')->get());
        $this->assertTrue(at([1, true], 1)->get());
        $this->assertNull(at([1, true], 2)->get());
    }

    public function testAtWithIterable(): void
    {
        $this->assertNull(at(new FooIterable(), 'b')->get());
    }
}
