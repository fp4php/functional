<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\unique;

final class UniqueTest extends TestCase
{
    public function testUnique(): void
    {
        $this->assertEquals(
            [1],
            unique(
                [1, 1, 1, 1, 2, 3, 3],
                fn(int $v) => 1
            )
        );
    }
}
