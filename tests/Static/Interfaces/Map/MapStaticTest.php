<?php

declare(strict_types=1);

namespace Tests\Static\Interfaces\Map;

use Fp\Collections\Map;
use Fp\Collections\Seq;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Tests\Mock\Foo;

final class MapStaticTest
{
    /**
     * @param Map<Foo, int> $coll
     * @return None
     */
    public function testToAssocArrayWithInvalidInput(Map $coll): Option
    {
        return $coll->toAssocArray();
    }

    /**
     * @param Map<string, int> $coll
     * @return Some<array<string, int>>
     */
    public function testToAssocArrayWithValidInput(Map $coll): Option
    {
        return $coll->toAssocArray();
    }

    /**
     * @param Seq<array{string, int}> $coll
     * @return array<string, int>
     */
    public function testToAssocArrayFromSeq(Seq $coll): array
    {
        return $coll
            ->toHashMap(fn($pair) => $pair)
            ->toAssocArray()
            ->get();
    }
}
