<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\groupBy;
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
                /** @param array{id: int, sum: int} $a */
                fn(array $a) => $a['id'],
                /** @param array{id: int, sum: int} $a */
                fn(array $a) => [$a['sum']],
                /**
                 * @param list<int> $old
                 * @param list<int> $new
                 */
                fn(array $old, array $new) => array_merge($old, $new),
            )
        );
    }
}
