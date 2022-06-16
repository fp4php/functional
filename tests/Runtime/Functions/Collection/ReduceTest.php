<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\reduce;

final class ReduceTest extends TestCase
{
    public function testReduce(): void
    {
        /** @var list<string> $c */
        $c = ['a', 'b', 'c'];

        $this->assertEquals(
            'abc',
            reduce($c, fn(string $acc, string $v) => $acc . $v)->get()
        );
    }
}
