<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\group;

final class GroupTest extends TestCase
{
    public function testGroup(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            ['y' => ['a' => 1, 'c' => 3], 'x' => ['b' => 2, 'd' => 4]],
            group(
                $c,
                fn(int $v, string $k) => ($v % 2 === 0) ? 'x' : 'y'
            )
        );
    }
}
