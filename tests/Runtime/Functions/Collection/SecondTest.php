<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\second;

final class SecondTest extends TestCase
{
    public function testSecond(): void
    {
        $this->assertEquals(
            'b',
            second(['a', 'b', 'c'])->get()
        );
    }
}
