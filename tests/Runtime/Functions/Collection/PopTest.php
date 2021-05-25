<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\pop;

final class PopTest extends TestCase
{
    public function testPop(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            [3, [1, 2]],
            pop($c)->get()
        );
    }
}
