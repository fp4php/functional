<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\groupMapReduce;

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
}
