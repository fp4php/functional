<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\mkString;

final class MkStringTest extends TestCase
{
    public function testMkString(): void
    {
        $this->assertEquals('1,2,3', mkString(['1', '2', '3']));
        $this->assertEquals('(1, 2, 3)', mkString(['1', '2', '3'], '(', ', ', ')'));
        $this->assertEquals('', mkString([]));
        $this->assertEquals('()', mkString([], '(', ', ', ')'));
    }
}
