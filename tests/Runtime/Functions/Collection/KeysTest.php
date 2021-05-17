<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\keys;

final class KeysTest extends TestCase
{
    public function testKeys(): void
    {
        $this->assertEquals(
            ['a', 'b', 'c'],
            keys(['a' => 1, 'b' => 2, 'c' => 3])
        );

        $this->assertEquals(
            [0, 1, 2, 3],
            keys([1, 2, 3, 4])
        );
    }
}
