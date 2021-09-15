<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\map;

final class MapStaticTest
{
    /**
     * @param list<int> $coll
     * @return list<numeric-string>
     */
    public function testListOfInt(array $coll): array
    {
        return map($coll, fn(int $value, int $key) => (string) $value);
    }

    /**
     * @param array<string, int> $coll
     * @return array<string, numeric-string>
     */
    public function testArrayOfInt(array $coll): array
    {
        return map($coll, fn(int $value, string $key) => (string) $value);
    }
}
