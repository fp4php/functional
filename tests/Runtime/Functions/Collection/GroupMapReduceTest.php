<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\groupMapReduce;
use function Fp\Collection\groupMapReduceKV;

final class GroupMapReduceTest extends TestCase
{
    public function testGroup(): void
    {
        $this->assertEquals(
            [
                10 => [10, 15, 20],
                20 => [10, 15],
                30 => [20],
            ],
            groupMapReduce(
                [
                    ['id' => 10, 'sum' => 10],
                    ['id' => 10, 'sum' => 15],
                    ['id' => 10, 'sum' => 20],
                    ['id' => 20, 'sum' => 10],
                    ['id' => 20, 'sum' => 15],
                    ['id' => 30, 'sum' => 20],
                ],
                fn(array $a) => $a['id'],
                fn(array $a) => [$a['sum']],
                fn(array $old, array $new) => array_merge($old, $new),
            )
        );
    }

    public function testGroupMapKV(): void
    {
        /** @var array<int, int> $c */
        $c = [
            1 => 1, // 2
            3 => 2, // 5
            5 => 3, // 8
            7 => 4, // 11
        ];

        $this->assertEquals(
            [
                'k+v=even-sum' => 10,
                'k+v=odd-sum' => 16,
            ],
            groupMapReduceKV($c,
                group: fn($k, $v) => ($k + $v) % 2 === 0 ? 'k+v=even-sum' : 'k+v=odd-sum',
                map: fn($k, $v) => $k + $v,
                reduce: fn($old, $new) => $old + $new),
        );
    }
}
