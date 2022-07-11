<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\reverse;

final class ReverseStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return array<string, int>
     */
    public function testWithArray(array $coll): array
    {
        return reverse($coll);
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<string, int>
     */
    public function testWithNonEmptyArray(array $coll): array
    {
        return reverse($coll);
    }

    /**
     * @param list<int> $coll
     * @return list<int>
     */
    public function testWithList(array $coll): array
    {
        return reverse($coll);
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-list<int>
     */
    public function testWithNonEmptyList(array $coll): array
    {
        return reverse($coll);
    }
}
