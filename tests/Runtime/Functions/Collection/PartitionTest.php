<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\partition;

final class PartitionTest extends TestCase
{
    public function testPartition(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            [[2, 4], [1, 3]],
            partition($c, fn(int $v) => $v % 2 === 0)
        );

        $this->assertEquals(
            [[1], [2], [3], [4], []],
            partition(
                $c,
                fn(int $v) => $v === 1,
                fn(int $v) => $v === 2,
                fn(int $v) => $v === 3,
                fn(int $v) => $v === 4,
            )
        );
    }
}
