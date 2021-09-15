<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\groupBy;

final class GroupStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return array<non-empty-string, array<string, int>>
     */
    public function testWithArray(array $coll): array
    {
        return groupBy(
            $coll,
            fn(int $v, string $k) => $k . "10"
        );
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<non-empty-string, non-empty-array<string, int>>
     */
    public function testWithNonEmptyArray(array $coll): array
    {
        return groupBy(
            $coll,
            fn(int $v, string $k) => $k . "10"
        );
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-array<non-empty-string, non-empty-array<int, int>>
     */
    public function testWithNonEmptyList(array $coll): array
    {
        return groupBy(
            $coll,
            fn(int $v, int $k) => $k . "10"
        );
    }

    /**
     * @param list<string> $coll
     * @return array<non-empty-string, array<int, string>>
     */
    public function testWithListInferGroupKey(array $coll): array
    {
        return groupBy(
            $coll,
            fn(string $value) => $value . "10"
        );
    }

    /**
     * @param array<non-empty-string, string> $coll
     * @return array<non-empty-string, array<non-empty-string, string>>
     */
    public function testWithArrayInferGroupKey(array $coll): array
    {
        return groupBy(
            $coll,
            fn(string $value) => $value . "10"
        );
    }

    /**
     * @psalm-type Alias = string
     * @param array<Alias, int> $coll
     * @return array<int, array<string, int>>
     */
    public function testWithArrayAndGroupKeyAsTypeAlias(array $coll): array
    {
        return groupBy(
            $coll,
            fn(int $value) => $value
        );
    }
}
