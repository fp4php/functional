<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use Tests\Mock\SubBar;

use function Fp\Collection\filter;
use function Fp\Collection\filterKV;
use function Fp\Collection\filterMap;
use function Fp\Collection\filterOf;
use function Fp\Collection\filterNotNull;

final class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $this->assertEquals([1], filter(
            ['a' => 1, 2],
            fn(int $v) => $v < 2
        ));

        $this->assertEquals(['a' => 1], filter(
            ['a' =>  1, 'b' => 2],
            fn(int $v) => $v < 2,
            true
        ));
    }

    public function testFilterKV(): void
    {
        $this->assertEquals(
            [2],
            filterKV(
                ['fst' => 1, 'snd' => 2, 'thd' => 3],
                fn($k, $v): bool => $k !== 'fst' && $v !== 3,
            ),
        );

        $this->assertEquals(
            ['snd' => 2],
            filterKV(
                ['fst' => 1, 'snd' => 2, 'thd' => 3],
                fn($k, $v): bool => $k !== 'fst' && $v !== 3,
                true,
            ),
        );
    }

    public function testFilterNotNull(): void
    {
        $this->assertEquals(
            [0 => 1, 1 => 2, 2 => 3, 4 => 4, 5 => 5, 7 => 6],
            filterNotNull([1, 2, 3, null, 4, 5, null, 6], true)
        );

        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            filterNotNull([1, 2, 3, null, 4, 5, null, 6], false)
        );
    }

    public function testFilterOf(): void
    {
        $foo = new Foo(1);
        $bar = new Bar(true);
        $subBar = new SubBar(true);

        $this->assertEquals(
            [2 => $bar],
            filterOf([1, $foo, $bar, 4], Bar::class, true)
        );

        $this->assertEquals(
            ['2' => $bar],
            filterOf([1, $foo, $bar, 4], Bar::class, true)
        );

        $this->assertEquals(
            [$bar],
            filterOf([1, $foo, $bar, 4], Bar::class, false)
        );

        $this->assertEquals(
            [2 => $bar],
            filterOf([1, $subBar, $bar, 4], Bar::class, true, true)
        );
    }

    public function testFilterMap(): void
    {
        $this->assertEquals([1], filterMap(
            ['a' => 1, 2],
            fn(int $v) => $v < 2 ? Option::some($v) : Option::none(),
        ));

        $this->assertEquals(['a' => 1], filterMap(
            ['a' =>  1, 'b' => 2],
            fn(int $v) => $v < 2 ? Option::some($v) : Option::none(),
            true
        ));
    }
}
