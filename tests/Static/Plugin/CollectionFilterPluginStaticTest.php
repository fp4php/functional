<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use Fp\Collections\Map;
use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Streams\Stream;

final class CollectionFilterPluginStaticTest
{
    /**
     * @psalm-param array{
     *     ArrayList: ArrayList<1|null|2>,
     *     LinkedList: LinkedList<1|null|2>,
     *     HashSet: HashSet<1|null|2>,
     *     NonEmptyArrayList: NonEmptyArrayList<1|null|2>,
     *     NonEmptyLinkedList: NonEmptyLinkedList<1|null|2>,
     *     NonEmptyHashSet: NonEmptyHashSet<1|null|2>,
     *     Seq: Seq<1|null|2>,
     *     Set: Set<1|null|2>,
     *     NonEmptySeq: NonEmptySeq<1|null|2>,
     *     NonEmptySet: NonEmptySet<1|null|2>,
     *     Map: Map<string, null|int>,
     *     HashMap: HashMap<string, null|int>,
     *     Stream: Stream<1|null|2>,
     * } $in
     *
     * @psalm-return array{
     *     ArrayList: ArrayList<1|2>,
     *     LinkedList: LinkedList<1|2>,
     *     HashSet: HashSet<1|2>,
     *     NonEmptyArrayList: ArrayList<1|2>,
     *     NonEmptyLinkedList: LinkedList<1|2>,
     *     NonEmptyHashSet: HashSet<1|2>,
     *     Seq: Seq<1|2>,
     *     Set: Set<1|2>,
     *     NonEmptySeq: Seq<1|2>,
     *     NonEmptySet: Set<1|2>,
     *     Map: Map<string, int>,
     *     HashMap: HashMap<string, int>,
     *     Stream: Stream<1|2>,
     * }
     */
    public function testFilter(array $in): array
    {
        return [
            'ArrayList' => $in['ArrayList']->filter(fn($e) => null !== $e),
            'LinkedList' => $in['LinkedList']->filter(fn($e) => null !== $e),
            'HashSet' => $in['HashSet']->filter(fn($e) => null !== $e),
            'NonEmptyArrayList' => $in['NonEmptyArrayList']->filter(fn($e) => null !== $e),
            'NonEmptyLinkedList' => $in['NonEmptyLinkedList']->filter(fn($e) => null !== $e),
            'NonEmptyHashSet' => $in['NonEmptyHashSet']->filter(fn($e) => null !== $e),
            'Seq' => $in['Seq']->filter(fn($e) => null !== $e),
            'Set' => $in['Set']->filter(fn($e) => null !== $e),
            'NonEmptySeq' => $in['NonEmptySeq']->filter(fn($e) => null !== $e),
            'NonEmptySet' => $in['NonEmptySet']->filter(fn($e) => null !== $e),
            'Map' => $in['Map']->filter(fn($e) => null !== $e),
            'HashMap' => $in['HashMap']->filter(fn($e) => null !== $e),
            'Stream' => $in['Stream']->filter(fn($e) => null !== $e),
        ];
    }

    /**
     * @psalm-param array{
     *     ArrayList: ArrayList<int|null>,
     *     LinkedList: LinkedList<int|null>,
     *     NonEmptyArrayList: NonEmptyArrayList<int|null>,
     *     NonEmptyLinkedList: NonEmptyLinkedList<int|null>,
     *     Seq: Seq<int|null>,
     *     NonEmptySeq: NonEmptySeq<int|null>,
     *     Set: Set<int|null>,
     *     NonEmptySet: NonEmptySet<int|null>,
     *     Map: Map<int|string, null|int>,
     *     HashMap: HashMap<int|string, null|int>
     * } $in
     *
     * @psalm-return array{
     *     Map: Map<string, int>,
     *     HashMap: HashMap<string, int>,
     * }
     */
    public function testFilterKV(array $in): array
    {
        return [
            'Map' => $in['Map']->filterKV(fn($k, $v) => is_string($k) && null !== $v),
            'HashMap' => $in['HashMap']->filterKV(fn($k, $v) => is_string($k) && null !== $v),
        ];
    }

    /**
     * @psalm-param array{
     *     ArrayList: ArrayList<1|null|2>,
     *     LinkedList: LinkedList<1|null|2>,
     *     HashSet: HashSet<1|null|2>,
     *     NonEmptyArrayList: NonEmptyArrayList<1|null|2>,
     *     NonEmptyLinkedList: NonEmptyLinkedList<1|null|2>,
     *     NonEmptyHashSet: NonEmptyHashSet<1|null|2>,
     *     Seq: Seq<1|null|2>,
     *     Set: Set<1|null|2>,
     *     NonEmptySeq: NonEmptySeq<1|null|2>,
     *     NonEmptySet: NonEmptySet<1|null|2>,
     *     Stream: Stream<1|null|2>,
     * } $in
     * @psalm-return array{
     *     ArrayList: ArrayList<1|2>,
     *     LinkedList: LinkedList<1|2>,
     *     HashSet: HashSet<1|2>,
     *     NonEmptyArrayList: ArrayList<1|2>,
     *     NonEmptyLinkedList: LinkedList<1|2>,
     *     NonEmptyHashSet: HashSet<1|2>,
     *     Seq: Seq<1|2>,
     *     Set: Set<1|2>,
     *     NonEmptySeq: Seq<1|2>,
     *     NonEmptySet: Set<1|2>,
     *     Stream: Stream<1|2>,
     * }
     */
    public function testFilterNotNull(array $in): array
    {
        return [
            'ArrayList' => $in['ArrayList']->filterNotNull(),
            'LinkedList' => $in['LinkedList']->filterNotNull(),
            'HashSet' => $in['HashSet']->filterNotNull(),
            'NonEmptyArrayList' => $in['NonEmptyArrayList']->filterNotNull(),
            'NonEmptyLinkedList' => $in['NonEmptyLinkedList']->filterNotNull(),
            'NonEmptyHashSet' => $in['NonEmptyHashSet']->filterNotNull(),
            'Seq' => $in['Seq']->filterNotNull(),
            'Set' => $in['Set']->filterNotNull(),
            'NonEmptySeq' => $in['NonEmptySeq']->filterNotNull(),
            'NonEmptySet' => $in['NonEmptySet']->filterNotNull(),
            'Stream' => $in['Stream']->filterNotNull(),
        ];
    }
}
