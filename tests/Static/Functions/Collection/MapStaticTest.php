<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\map;
use function Fp\Collection\mapWithKey;

final class MapStaticTest
{
    /**
     * @param list<int> $coll
     * @return list<numeric-string>
     */
    public function testMapListToList(array $coll): array
    {
        return map($coll, fn(int $value) => (string) $value);
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-list<numeric-string>
     */
    public function testMapNonEmptyListToNonEmptyList(array $coll): array
    {
        return map($coll, fn(int $value) => (string) $value);
    }

    /**
     * @param array<string, int> $coll
     * @return array<string, numeric-string>
     */
    public function testMapArrayToArray(array $coll): array
    {
        return map($coll, fn(int $value) => (string) $value);
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<string, numeric-string>
     */
    public function testMapNonEmptyArrayToNonEmptyToArray(array $coll): array
    {
        return map($coll, fn(int $value) => (string) $value);
    }

    /**
     * @param list<int> $coll
     * @return list<non-empty-string>
     */
    public function testMapWithKeyListToList(array $coll): array
    {
        return mapWithKey($coll, fn(int $key, int $value) => "{$key}-{$value}");
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-list<non-empty-string>
     */
    public function testMapWithKeyNonEmptyListToNonEmptyList(array $coll): array
    {
        return mapWithKey($coll, fn(int $key, int $value) => "{$key}-{$value}");
    }

    /**
     * @param array<string, int> $coll
     * @return array<string, non-empty-string>
     */
    public function testMapWithKeyArrayToArray(array $coll): array
    {
        return mapWithKey($coll, fn(string $key, int $value) => "{$key}-{$value}");
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<string, non-empty-string>
     */
    public function testMapWithKeyNonEmptyArrayToNonEmptyToArray(array $coll): array
    {
        return mapWithKey($coll, fn(string $key, int $value) => "{$key}-{$value}");
    }
}
