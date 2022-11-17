<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\filter;
use function Fp\Collection\filterKV;
use function Fp\Collection\filterMap;
use function Fp\Collection\filterNotNull;

final class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $this->assertEquals([1], filter(
            [1, 2],
            fn(int $v) => $v < 2
        ));

        $this->assertEquals(['a' => 1], filter(
            ['a' =>  1, 'b' => 2],
            fn(int $v) => $v < 2,
        ));
    }

    public function testFilterKV(): void
    {
        $this->assertEquals(
            ['snd' => 2],
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
            ),
        );
    }

    public function testFilterNotNull(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            filterNotNull([1, 2, 3, null, 4, 5, null, 6])
        );

        $this->assertEquals(
            ['fst' => 1, 'snd' => 2],
            filterNotNull(['fst' => 1, 'snd' => 2, 'thr' => null]),
        );
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(['a' => 1], filterMap(
            ['a' => 1, 2],
            fn(int $v) => $v < 2 ? Option::some($v) : Option::none(),
        ));

        $this->assertEquals(['a' => 1], filterMap(
            ['a' =>  1, 'b' => 2],
            fn(int $v) => $v < 2 ? Option::some($v) : Option::none(),
        ));
    }
}
