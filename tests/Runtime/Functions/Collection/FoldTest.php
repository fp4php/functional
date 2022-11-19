<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\fold;

final class FoldTest extends TestCase
{
    public function testFold(): void
    {
        $this->assertEquals('abc', fold('', ['a', 'b', 'c'])(fn($acc, $v) => $acc . $v));
    }
}
