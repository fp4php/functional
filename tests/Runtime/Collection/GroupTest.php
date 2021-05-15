<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Collection\at;
use function Fp\Collection\copyCollection;
use function Fp\Collection\every;
use function Fp\Collection\filter;
use function Fp\Collection\first;
use function Fp\Collection\flatMap;
use function Fp\Collection\fold;
use function Fp\Collection\group;
use function Fp\Collection\head;
use function Fp\Collection\last;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\pluck;
use function Fp\Collection\pop;
use function Fp\Collection\reduce;
use function Fp\Collection\reverse;
use function Fp\Collection\second;
use function Fp\Collection\shift;
use function Fp\Collection\any;
use function Fp\Collection\tail;

final class GroupTest extends TestCase
{
    public function testGroup(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            ['y' => ['a' => 1, 'c' => 3], 'x' => ['b' => 2, 'd' => 4]],
            group(
                $c,
                fn(int $v, string $k) => ($v % 2 === 0) ? 'x' : 'y'
            )
        );
    }
}
