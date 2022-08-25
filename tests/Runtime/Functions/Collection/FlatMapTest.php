<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\flatMap;
use function Fp\Collection\flatMapKV;

final class FlatMapTest extends TestCase
{
    public function testFlatMap(): void
    {
        $c = [1, 4];

        $this->assertEquals(
            [0, 1, 2, 3, 4, 5],
            flatMap(
                $c,
                fn(int $v) => [$v - 1, $v, $v + 1]
            )
        );
    }

    public function testFlatMapKV(): void
    {
        $c = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals(['fst', 1, 'snd', 2, 'thr', 3], flatMapKV($c, fn($k, $v) => [$k, $v]));
    }
}
