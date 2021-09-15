<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Functional\Option\Option;

use function Fp\Collection\shift;

final class ShiftStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return Option<array{int, list<int>}>
     */
    public function testWithArray(array $coll): Option
    {
        return shift($coll);
    }
}
