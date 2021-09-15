<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\fold;

final class FoldStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return int
     */
    public function testWithArray(array $coll): int
    {
        return fold(
            0,
            $coll,
            fn(int $acc, int $v) => $acc + $v
        );
    }
}
