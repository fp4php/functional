<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\shift;

final class ShiftTest extends TestCase
{
    public function testShift(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            [1, [2, 3]],
            shift($c)->get()
        );
    }
}
