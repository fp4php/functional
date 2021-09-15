<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\HashSet;
use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Collections\Map;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\NonEmptyMap;
use Fp\Streams\Stream;

final class CollectionFilterPluginStaticTest
{
    /**
     * @psalm-param array{
     *     ArrayList<1|null|2>,
     *     LinkedList<1|null|2>,
     *     HashSet<1|null|2>,
     *     HashMap<'a'|'b'|'c', 1|null|2>,
     *     NonEmptyArrayList<1|null|2>,
     *     NonEmptyLinkedList<1|null|2>,
     *     NonEmptyHashSet<1|null|2>,
     *     NonEmptyHashMap<'a'|'b'|'c', 1|null|2>,
     *     Seq<1|null|2>,
     *     Set<1|null|2>,
     *     Map<'a'|'b'|'c', 1|null|2>,
     *     NonEmptySeq<1|null|2>,
     *     NonEmptySet<1|null|2>,
     *     NonEmptyMap<'a'|'b'|'c', 1|null|2>,
     *     Stream<1|null|2>,
     * } $in
     * @psalm-return array{
     *     ArrayList<1|2>,
     *     LinkedList<1|2>,
     *     HashSet<1|2>,
     *     HashMap<'a'|'b', 1|2>,
     *     ArrayList<1|2>,
     *     LinkedList<1|2>,
     *     HashSet<1|2>,
     *     HashMap<'a'|'b', 1|2>,
     *     Seq<1|2>,
     *     Set<1|2>,
     *     Map<'a'|'b', 1|2>,
     *     Seq<1|2>,
     *     Set<1|2>,
     *     Map<'a'|'b'|'c', 1|null|2>,
     *     Stream<1|2>,
     * }
     */
    public function testFilter(array $in): array
    {
        return [
            $in[0]->filter(fn($e) => null !== $e),
            $in[1]->filter(fn($e) => null !== $e),
            $in[2]->filter(fn($e) => null !== $e),
            $in[3]->filter(fn($e) => null !== $e->value && $e->key !== "c"),
            $in[4]->filter(fn($e) => null !== $e),
            $in[5]->filter(fn($e) => null !== $e),
            $in[6]->filter(fn($e) => null !== $e),
            $in[7]->filter(fn($e) => null !== $e->value && $e->key !== "c"),
            $in[8]->filter(fn($e) => null !== $e),
            $in[9]->filter(fn($e) => null !== $e),
            $in[10]->filter(fn($e) => null !== $e->value && $e->key !== "c"),
            $in[11]->filter(fn($e) => null !== $e),
            $in[12]->filter(fn($e) => null !== $e),
            $in[13]->filter(fn($e) => null !== $e->value && $e->key !== "c"),
            $in[14]->filter(fn($e) => null !== $e),
        ];
    }

    /**
     * @psalm-param array{
     *     ArrayList<1|null|2>,
     *     LinkedList<1|null|2>,
     *     HashSet<1|null|2>,
     *     NonEmptyArrayList<1|null|2>,
     *     NonEmptyLinkedList<1|null|2>,
     *     NonEmptyHashSet<1|null|2>,
     *     Seq<1|null|2>,
     *     Set<1|null|2>,
     *     NonEmptySeq<1|null|2>,
     *     NonEmptySet<1|null|2>,
     *     Stream<1|null|2>,
     * } $in
     * @psalm-return array{
     *     ArrayList<1|2>,
     *     LinkedList<1|2>,
     *     HashSet<1|2>,
     *     ArrayList<1|2>,
     *     LinkedList<1|2>,
     *     HashSet<1|2>,
     *     Seq<1|2>,
     *     Set<1|2>,
     *     Seq<1|2>,
     *     Set<1|2>,
     *     Stream<1|2>,
     * }
     */
    public function testFilterNotNull(array $in): array
    {
        return [
            $in[0]->filterNotNull(),
            $in[1]->filterNotNull(),
            $in[2]->filterNotNull(),
            $in[3]->filterNotNull(),
            $in[4]->filterNotNull(),
            $in[5]->filterNotNull(),
            $in[6]->filterNotNull(),
            $in[7]->filterNotNull(),
            $in[8]->filterNotNull(),
            $in[9]->filterNotNull(),
            $in[10]->filterNotNull(),
        ];
    }
}
