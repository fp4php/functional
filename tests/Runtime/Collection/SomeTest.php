<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\some;

final class SomeTest extends TestCase
{
    public function testSome(): void
    {
        $this->assertTrue(some(
            [1, 2],
            fn(int $v) => $v < 2
        ));

        $this->assertFalse(some(
            [2, 3 ,4],
            fn(int $v) => $v < 2
        ));
    }
}
