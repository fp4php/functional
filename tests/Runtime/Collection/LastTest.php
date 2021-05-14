<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\last;

final class LastTest extends TestCase
{
    public function testLast(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(
            3,
            last($c)->get()
        );
    }
}
