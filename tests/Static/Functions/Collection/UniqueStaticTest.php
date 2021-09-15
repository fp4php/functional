<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\unique;

final class UniqueStaticTest
{
    /**
     * @param iterable<string, int> $coll
     * @return list<int>
     */
    public function testWithIterable(iterable $coll): array
    {
        return unique($coll, fn(int $v) => $v);
    }
}
