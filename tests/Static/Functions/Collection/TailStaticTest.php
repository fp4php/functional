<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\tail;

final class TailStaticTest
{
    /**
     * @param non-empty-array<string, int> $coll
     * @return list<int>
     */
    public function testWithArray(array $coll): array
    {
        return tail($coll);
    }
}
