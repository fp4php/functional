<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Functional\Option\Option;
use function Fp\Collection\last;

final class LastStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return Option<int>
     */
    public function testWithArray(array $coll): Option
    {
        return last($coll);
    }
}
