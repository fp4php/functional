<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\zip;

final class ZipStaticTest
{
    /**
     * @param array<string, int> $coll1
     * @param iterable<string, bool> $coll2
     * @return list<array{int, bool}>
     */
    public function testWithIterable(array $coll1, iterable $coll2): array
    {
        return zip($coll1, $coll2);
    }
}
