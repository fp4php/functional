<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\reverse;

final class ReverseTest extends TestCase
{
    public function testReverse(): void
    {
        $this->assertEquals(
            ['b', 'a'],
            reverse(['a', 'b'])
        );

        $this->assertEquals(
            ['k1' => 'b', 'k2' => 'a'],
            reverse(['k2' => 'a', 'k1' => 'b'])
        );
    }
}
