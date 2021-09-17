<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\StaticStorage;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;

final class StorageStaticTest
{
    const STATIC_STORAGE = [
        'a' => 1,
        'b' => 2,
    ];

    /**
     * @psalm-var NonEmptyMap & StaticStorage<self::STATIC_STORAGE>
     */
    private NonEmptyMap $map;

    public function __construct()
    {
        $this->map = NonEmptyHashMap::collectNonEmpty(self::STATIC_STORAGE);
    }

    /**
     * @return Some<1>
     */
    public function getSome(): Option
    {
        return $this->map->get('a');
    }

    /**
     * @psalm-return 1
     */
    public function getOne(): int
    {
        return $this->getSome()->get();
    }

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
