<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Generator;

use function Fp\Collection\chunks;

final class ChunksStaticTest
{
    /**
     * @param array<int, string> $coll
     * @return Generator<list<string>>
     */
    public function testChunksWithArray(array $coll): Generator
    {
        return chunks($coll, 2);
    }
}
