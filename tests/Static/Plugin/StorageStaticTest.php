<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Some;

final class StorageStaticTest
{
    /**
     * @param HashMap<'a'|'b', 1|2> $map
     * @return Some<1|2>
     */
    public function testGetFromStaticStorageFallback($map): Some
    {
        return $map->get('a');
    }

    /**
     * @return array{
     *     Some<1>,
     *     Some<1>,
     *     Some<1>,
     *     Some<1>,
     *     Some<1>,
     *     Some<1>,
     */
    public function testGetFromStaticStorage(): array
    {
        return [
            HashMap::collect(['a' => 1, 'b' => 2])->get('a'),
            HashMap::collectPairs([['a', 1], ['b',  2]])->get('a'),
            NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->get('a'),
            NonEmptyHashMap::collectUnsafe(['a' => 1, 'b' => 2])->get('a'),
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b',  2]])->get('a'),
            NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b',  2]])->get('a'),
        ];
    }
}
