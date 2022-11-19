<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\last;
use function Fp\Collection\lastKV;
use function Fp\Collection\lastMap;

final class LastTest extends TestCase
{
    public function testLast(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(Option::some(3), last($c));
    }

    public function testLastKV(): void
    {
        $c = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals(Option::some(2), lastKV($c, fn($k, $v) => $k === 'snd' && $v === 2));
    }

    public function testLastMap(): void
    {
        $this->assertEquals(
            Option::none(),
            lastMap(['fst', 'snd', 'thr'], fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );

        $this->assertEquals(
            Option::some(2),
            lastMap(['zero', '1', '2'], fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );
    }
}
