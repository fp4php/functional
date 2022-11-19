<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Callable;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Baz;
use Tests\Mock\Foo;

use function Fp\Callable\compose;
use function Fp\Callable\ctor;
use function Fp\Callable\partial;
use function Fp\Callable\partialLeft;
use function Fp\Callable\partialRight;

final class CallableTest extends TestCase
{
    public function testCtor(): void
    {
        $fooCtor = ctor(Foo::class);
        $bazCtor = ctor(Baz::class);

        $this->assertEquals(new Foo(a: 1, b: true, c: true), $fooCtor(1));
        $this->assertEquals(new Foo(a: 1, b: false, c: true), $fooCtor(1, false));
        $this->assertEquals(new Foo(a: 1, b: false, c: false), $fooCtor(1, false, false));
        $this->assertEquals(new Baz(), $bazCtor());
        $this->assertEquals(
            Option::some(new Foo(a: 1, b: true, c: false)),
            Option::some(['a' => 1, 'b' => true, 'c' => false])->mapN($fooCtor),
        );
    }

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
