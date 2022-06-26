<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\forAll;

final class FoldAllTest extends TestCase
{
    public function testWithArray(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];
        $buffer = [];

        forAll($c, function(int $v) use (&$buffer) {
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

        forAll($items, function(int $v) use (&$buffer) {
            /** @var list<int> $buffer */
            $buffer[] = $v;
        });

        $this->assertEquals([], $buffer);
    }
}
