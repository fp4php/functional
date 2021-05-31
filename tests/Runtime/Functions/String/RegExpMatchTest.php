<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\String;

use PHPUnit\Framework\TestCase;

use function Fp\Json\regExpMatch;

final class RegExpMatchTest extends TestCase
{
    public function testRegExpMatch(): void
    {
        $this->assertEquals('abc', regExpMatch('/[a-z]+(c)/', 'abc', 0)->get());
        $this->assertEquals('c', regExpMatch('/[a-z]+(c)/', 'abc', 1)->get());
    }
}
