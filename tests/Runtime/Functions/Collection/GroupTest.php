<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\groupBy;

final class GroupTest extends TestCase
{
    public function testGroup(): void
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
}
