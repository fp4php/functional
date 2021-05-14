<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Tests\PhpBlockTestCase;

final class PartitionTest extends PhpBlockTestCase
{
    public function testWithOnePredicate(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $result = \Fp\Collection\partition(
                [2, 3, 4, 5], 
                fn(int $v) => $v % 2 === 0
            );
        ';

        $this->assertBlockType($phpBlock, 'array{0: array<0|1|2|3, 2|3|4|5>, 1: array<0|1|2|3, 2|3|4|5>}');
    }

    public function testWithTwoPredicates(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $result = \Fp\Collection\partition(
                [1],
                fn(int $v) => $v % 2 === 0,
                fn(int $v) => $v % 2 === 1,
            );
        ';

        $this->assertBlockType($phpBlock, 'array{0: array<0, 1>, 1: array<0, 1>, 2: array<0, 1>}');
    }
}
