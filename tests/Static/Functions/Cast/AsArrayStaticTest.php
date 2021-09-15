<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Cast;

use function Fp\Cast\asArray;

final class AsArrayStaticTest
{
    /**
     * @param iterable<string, int> $coll
     * @return array<string, int>
     */
    public function testWithIterable(iterable $coll): array
    {
        return asArray($coll);
    }
}
