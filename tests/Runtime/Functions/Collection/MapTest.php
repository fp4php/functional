<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\map;

final class MapTest extends TestCase
{
    public function testMap(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            ['a' => '2', 'b' => '3', 'c' => '4'],
            map(
                $c,
                fn(int $v) => (string) ($v + 1)
            )
        );
    }
}
