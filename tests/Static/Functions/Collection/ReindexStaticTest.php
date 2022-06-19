<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Collections\ArrayList;
use function Fp\Collection\reindex;
use function Fp\Collection\reindexWithKey;

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
     * @param array<string, int> $coll
     * @return array<non-empty-string, int>
     */
    public function testReindexWithKeyArray(array $coll): array
    {
        return reindexWithKey($coll, fn(string $k, int $v) => "key-{$k}-{$v}");
    }
}
