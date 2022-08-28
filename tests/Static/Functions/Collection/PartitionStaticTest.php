<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\partition;

final class PartitionStaticTest
{
    /**
     * @param list<int> $list
     * @return array{list<int>, list<int>}
     */
    public function testPartitionWithOnePredicate(array $list): array
    {
        return partition($list, fn(int $v) => $v % 2 === 0);
    }

    /**
     * @param list<int> $nums
     * @return array{list<int>, list<int>, list<int>}
     */
    public function testPartitionWithTwoPredicates(array $nums): array
    {
        return partition(
            $nums,
            fn(int $v) => $v % 2 === 0,
            fn(int $v) => $v % 2 === 1,
        );
    }
}
