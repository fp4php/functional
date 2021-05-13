<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\fold;

final class FoldTest extends TestCase
{
    public function testFold(): void
    {
        $c = ['a', 'b', 'c'];

        $this->assertEquals(
            'abc',
            fold(
                '',
                $c,
                fn(string $acc, string $v) => $acc . $v
            )
        );
    }
}
