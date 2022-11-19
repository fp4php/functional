<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\tap;

final class TapTest extends TestCase
{
    public function testWithArray(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];
        $buffer = [];

        tap($c, function(int $v) use (&$buffer) {
            /** @var list<int> $buffer */
            $buffer[] = $v;
        });

        $this->assertEquals([1, 2, 3], $buffer);
    }

    public function testWithEmptyArray(): void
    {
        /** @var array<string, int> $items */
        $items = [];

        $buffer = [];

        tap($items, function(int $v) use (&$buffer) {
            /** @var list<int> $buffer */
            $buffer[] = $v;
        });

        $this->assertEquals([], $buffer);
    }
}
