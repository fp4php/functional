<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Functional\Option\Option;

use function Fp\Collection\reduce;

final class ReduceStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return Option<int>
     */
    public function testWithArray(array $coll): Option
    {
        return reduce(
            $coll,
            fn(int $acc, int $v) => $acc + $v
        );
    }
}
