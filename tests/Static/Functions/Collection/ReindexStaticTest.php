<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\reindex;

final class ReindexStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return array<int, int>
     */
    public function testWithIterable(array $coll): array
    {
        return reindex(
            $coll,
            fn(int $v, string $k) => $v
        );
    }
}
