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
    public function testWithIterable(array $coll): array
    {
        return reverse($coll);
    }
}
