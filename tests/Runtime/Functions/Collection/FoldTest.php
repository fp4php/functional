<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\fold;

final class FoldTest extends TestCase
{
    public function testFold(): void
    {
        $c = ['a', 'b', 'c'];

        /** @var string $init */
        $init = '';

        $this->assertEquals(
            'abc',
            fold($init, $c, fn(string $acc, string $v) => $acc . $v)
        );
    }
}
