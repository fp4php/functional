<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\groupMap;

final class GroupMapTest extends TestCase
{
    public function testGroup(): void
    {
        $this->assertEquals(
            [
                10 => [11, 16, 21],
                20 => [11, 16],
                30 => [21],
            ],
            groupMap(
                [
                    ['id' => 10, 'sum' => 10],
                    ['id' => 10, 'sum' => 15],
                    ['id' => 10, 'sum' => 20],
                    ['id' => 20, 'sum' => 10],
                    ['id' => 20, 'sum' => 15],
                    ['id' => 30, 'sum' => 20],
                ],
                fn(array $a) => $a['id'],
                fn(array $a) => $a['sum'] + 1,
            )
        );
    }
}
