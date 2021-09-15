<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\HashMap;
use Fp\Functional\Option\Option;
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
     * @return Some<1>
     */
    public function testGetFromStaticStorage(): Option
    {
        $map1 = HashMap::collect(['a' => 1, 'b' => 2]);
        $map2 = HashMap::collectPairs([['a', 1], ['b',  2]]); // TODO

        return $map1->get('a');
    }
}
