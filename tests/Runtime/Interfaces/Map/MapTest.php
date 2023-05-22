<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Map;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\SimpleEnum;
use Tests\Mock\StringEnum;

final class MapTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals(
            'HashMap("k1" => 1, "k2" => 2, "k3" => 3)',
            (string) HashMap::collectPairs([
                ['k1', 1],
                ['k2', 2],
                ['k3', 3],
            ]),
        );
        $this->assertEquals(
            'HashMap("k1" => Some(1), "k2" => Some(2), "k3" => None)',
            (string) HashMap::collectPairs([
                ['k1', Option::some(1)],
                ['k2', Option::some(2)],
                ['k3', Option::none()],
            ]),
        );
        $this->assertEquals(
            'HashMap("k1" => 1, "k2" => 2, "k3" => 3)',
            HashMap::collectPairs([
                ['k1', 1],
                ['k2', 2],
                ['k3', 3],
            ])->toString(),
        );
        $this->assertEquals(
            'HashMap("k1" => Some(1), "k2" => Some(2), "k3" => None)',
            HashMap::collectPairs([
                ['k1', Option::some(1)],
                ['k2', Option::some(2)],
                ['k3', Option::none()],
            ])->toString(),
        );
    }

    public function testCasts(): void
    {
        $values = [['a', 1], ['b', 2]];

        /** @var HashMap<string, int> $nonEmptyHashMap */
        $nonEmptyHashMap = HashMap::collectPairs($values);

        /** @var HashMap<string, int> $emptyHashMap */
        $emptyHashMap = HashMap::collectPairs([]);

        $this->assertEquals(
            $values,
            $nonEmptyHashMap->toList(),
        );

        $this->assertEquals(
            Option::some($values),
            $nonEmptyHashMap->toNonEmptyList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyList(),
        );

        $this->assertEquals(
            [],
            $emptyHashMap->toArray(),
        );
        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyArray(),
        );

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            $nonEmptyHashMap->toArray(),
        );

        $this->assertEquals(
            Option::some(['a' => 1, 'b' => 2]),
            $nonEmptyHashMap->toNonEmptyArray(),
        );

        $this->assertEquals(
            LinkedList::collect($values),
            $nonEmptyHashMap->toLinkedList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyLinkedList::collectNonEmpty($values)),
            $nonEmptyHashMap->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            ArrayList::collect($values),
            $nonEmptyHashMap->toArrayList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyArrayList::collectNonEmpty($values)),
            $nonEmptyHashMap->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            HashSet::collect($values),
            $nonEmptyHashMap->toHashSet(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashSet::collectNonEmpty($values)),
            $nonEmptyHashMap->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            $nonEmptyHashMap,
            $nonEmptyHashMap->toHashMap(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashMap::collectPairsNonEmpty($values)),
            $nonEmptyHashMap->toNonEmptyHashMap(),
        );

        $this->assertEquals(
            Option::none(),
            $emptyHashMap->toNonEmptyHashMap(),
        );

        $this->assertEquals(
            Stream::emits([['fst', 1], ['snd', 2], ['thd', 3]])->toList(),
            HashMap::collectPairs([['fst', 1], ['snd', 2], ['thd', 3]])->toStream()->toList(),
        );
    }

    public function testContract(): void
    {
        $bar = new Bar(1);
        $hm = HashMap::collectPairs([[$bar, 'v1'], [new Foo(2), 'v2']]);
        $hm1 = HashMap::collectPairs([[[new Foo(1), new Foo(2)], 'v1']]);

        $this->assertEquals(
            'v2',
            $hm(new Foo(2))->get(),
        );

        $this->assertEquals(
            'v1',
            $hm1([new Foo(1), new Foo(2)])->get(),
        );

        $this->assertEquals(
            'v1',
            $hm($bar)->get(),
        );

        $this->assertNull($hm(new Bar(1))->get());
    }

    public function testCollisions(): void
    {
        $hm = HashMap::collectPairs([[1, 'v1'], [true, 'v2'], ['1', 'v3']]);
        $hm1 = HashMap::collectPairs([[0, 'v1'], [false, 'v2'], ['', 'v3']]);

        $this->assertEquals('v1', $hm(1)->get());
        $this->assertEquals('v2', $hm(true)->get());
        $this->assertEquals('v3', $hm('1')->get());

        $this->assertEquals('v1', $hm1(0)->get());
        $this->assertEquals('v2', $hm1(false)->get());
        $this->assertEquals('v3', $hm1('')->get());
    }

    public function testBackedEnum(): void
    {
        $actual = HashMap
            ::collectPairs([
                [StringEnum::FST, 1],
                [StringEnum::SND, 2],
                [StringEnum::THR, 3],
            ])
            ->appended(StringEnum::FST, 100)
            ->get(StringEnum::FST)
            ->toString();

        $this->assertEquals('Some(100)', $actual);
    }

    public function testUnitEnum(): void
    {
        $actual = HashMap
            ::collectPairs([
                [SimpleEnum::FST, 1],
                [SimpleEnum::SND, 2],
                [SimpleEnum::THR, 3],
            ])
            ->appended(SimpleEnum::FST, 100)
            ->get(SimpleEnum::FST)
            ->toString();

        $this->assertEquals('Some(100)', $actual);
    }

    public function testCount(): void
    {
        $this->assertEquals(2, HashMap::collectPairs([[1, 1], [2, 2]])->count());
    }

    public function testToMergedArray(): void
    {
        $shapes = [
            'f' => ['fst' => 1],
            's' => ['snd' => 2],
            't' => ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals($expected, HashMap::collect($shapes)->toMergedArray());
    }

    public function testToNonEmptyMergedArray(): void
    {
        $shapes = [
            'f' => ['fst' => 1],
            's' => ['snd' => 2],
            't' => ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals(Option::some($expected), HashMap::collect($shapes)->toNonEmptyMergedArray());
        $this->assertEquals(Option::none(), HashMap::empty()->toNonEmptyMergedArray());
    }
}
