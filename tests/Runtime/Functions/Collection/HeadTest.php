<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\head;

final class HeadTest extends TestCase
{
    public function testHead(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(
            1,
            head($c)->get()
        );
    }
}
