<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\reindex;
use function Fp\Collection\reindexKV;

final class ReindexStaticTest
{
    /**
     * @param array<string, int> $coll
     * @return array<non-empty-string, int>
     */
    public function testReindexArray(array $coll): array
    {
        return reindex($coll, fn(int $v) => "key-{$v}");
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<non-empty-string, int>
     */
    public function testReindexNonEmptyArray(array $coll): array
    {
        return reindex($coll, fn(int $v) => "key-{$v}");
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-array<non-empty-string, int>
     */
    public function testReindexNonEmptyList(array $coll): array
    {
        return reindex($coll, fn(int $v) => "key-{$v}");
    }

    /**
     * @param array<string, int> $coll
     * @return array<non-empty-string, int>
     */
    public function testReindexKVArray(array $coll): array
    {
        return reindexKV($coll, fn(string $k, int $v) => "key-{$k}-{$v}");
    }

    /**
     * @param non-empty-array<string, int> $coll
     * @return non-empty-array<non-empty-string, int>
     */
    public function testReindexKVNonEmptyArray(array $coll): array
    {
        return reindexKV($coll, fn(string $k, int $v) => "key-{$k}-{$v}");
    }

    /**
     * @param non-empty-list<int> $coll
     * @return non-empty-array<non-empty-string, int>
     */
    public function testReindexKVNonEmptyList(array $coll): array
    {
        return reindexKV($coll, fn(int $k, int $v) => "key-{$k}-{$v}");
    }
}
