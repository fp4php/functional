<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\groupBy;
use function Fp\Collection\groupByKV;

final class GroupTest extends TestCase
{
    public function testGroupKV(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            ['y' => [1, 3], 'x' => [2, 4]],
            groupBy(
                $c,
                fn(int $v) => ($v % 2 === 0) ? 'x' : 'y'
            )
        );
    }

    public function testGroupByKV(): void
    {
        $c = [
            1 => 1, // 2
            3 => 2, // 5
            5 => 3, // 8
            7 => 4, // 11
        ];

        $this->assertEquals(
            [
                'k+v=even' => [1, 3],
                'k+v=odd' => [2, 4],
            ],
            groupByKV($c, fn($k, $v) => ($k + $v) % 2 === 0 ? 'k+v=even' : 'k+v=odd'),
        );
    }
}
