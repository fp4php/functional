<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\Mock\Bar;
use Tests\Mock\Baz;
use Tests\Mock\Foo;

use function Fp\Collection\partitionT;

final class PartitionStaticTest
{
    /**
     * @param list<int> $list
     * @return array{list<int>, list<int>}
     */
    public function testPartitionWithOnePredicate(array $list): array
    {
        return partitionT($list, fn(int $v) => $v % 2 === 0);
    }

    /**
     * @param list<int> $nums
     * @return array{list<int>, list<int>, list<int>}
     */
    public function testPartitionWithTwoPredicates(array $nums): array
    {
        return partitionT(
            $nums,
            fn(int $v) => $v % 2 === 0,
            fn(int $v) => $v % 2 === 1,
        );
    }

    /**
     * @param list<Foo|Bar|Baz> $list
     * @return array{list<Foo>, list<Bar>, list<Baz>}
     */
    public function testExhaustiveInference(array $list): array
    {
        return partitionT($list, fn($i) => $i instanceof Foo, fn($i) => $i instanceof Bar);
    }
}
