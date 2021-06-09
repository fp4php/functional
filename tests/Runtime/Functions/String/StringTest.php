<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\String;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Json\lastExploded;
use function Fp\Json\regExpMatch;

final class StringTest extends TestCase
{
    public function testRegExpMatch(): void
    {
        $this->assertEquals('abc', regExpMatch('/[a-z]+(c)/', 'abc', 0)->get());
        $this->assertEquals('c', regExpMatch('/[a-z]+(c)/', 'abc', 1)->get());
    }

    public function testLastExploded(): void
    {
        $this->assertEquals(
            'Option',
            lastExploded(Option::class, '\\')
        );

        $this->assertEquals(
            'Option',
            lastExploded('Option', '\\')
        );

        $this->assertEquals(
            'Option',
            lastExploded('Fp\\Functional\Option/Option', ['\\', '/'])
        );
    }
}
