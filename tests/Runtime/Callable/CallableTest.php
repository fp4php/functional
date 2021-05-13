<?php

declare(strict_types=1);

namespace Tests\Runtime\Callable;

use PHPUnit\Framework\TestCase;

use function Fp\Callable\compose;
use function Fp\Callable\partial;
use function Fp\Callable\partialLeft;
use function Fp\Callable\partialRight;

final class CallableTest extends TestCase
{
    public function testCompose(): void
    {
        $aToB = fn(int $a): int => $a + 1;
        $bToC = fn(int $b): float => $b + 0.001;
        $cToD = fn(float $c): string => (string) $c;
        $composed = compose($aToB, $bToC, $cToD);

        $this->assertEquals('2.001', $composed(1));
    }

    public function testPartial(): void
    {
        $c = fn(string $a, string $b, string $c): string => $a . $b . $c;

        $this->assertEquals('abc', partial($c, 'a', 'b')('c'));
        $this->assertEquals('abc', partialLeft($c, 'a', 'b')('c'));
        $this->assertEquals('cba', partialRight($c, 'a', 'b')('c'));
    }
}
