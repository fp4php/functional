<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Cast;

use function Fp\Cast\asList;

final class AsListStaticTest
{
    /**
     * @param iterable<string, int> $coll
     * @return list<int>
     */
    public function testWithIterable(iterable $coll): array
    {
        return asList($coll);
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-list<int>
     */
    public function testWithNonEmptyArray(iterable $coll): array
    {
        return asList($coll);
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-list<int>
     */
    public function testWithNonEmptyList(iterable $coll): array
    {
        return asList($coll);
    }
}
