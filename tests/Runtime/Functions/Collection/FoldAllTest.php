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

        forAll($c, function(int $v, string $k) use (&$buffer) {
            /** @var array<string, int> $buffer */
            $buffer[$k] = $v;
        });

        $this->assertEquals($c, $buffer);
    }

    public function testWithEmptyArray(): void
    {
        $buffer = [];

        forAll([], function(int $v, string $k) use (&$buffer) {
            /** @var array<string, int> $buffer */
            $buffer[$k] = $v;
        });

        $this->assertEquals([], $buffer);
    }
}
