<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Util;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Util\regExpMatch;
use function Fp\Util\writeln;
use const PHP_EOL;

final class WritelnTest extends TestCase
{
    public function testWriteln(): void
    {
        writeln('Hi!');
        $this->expectOutputString('Hi!' . PHP_EOL);
    }
}
