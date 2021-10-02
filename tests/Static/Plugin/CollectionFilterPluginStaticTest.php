<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

final class CollectionFilterPluginStaticTest
{
    /**
     * @psalm-param array{
     *     \Fp\Collections\ArrayList<1|null|2>,
     *     \Fp\Collections\LinkedList<1|null|2>,
     *     \Fp\Collections\HashSet<1|null|2>,
     *     \Fp\Collections\HashMap<'a'|'b'|'c', 1|null|2>,
     *     \Fp\Collections\NonEmptyArrayList<1|null|2>,
     *     \Fp\Collections\NonEmptyLinkedList<1|null|2>,
     *     \Fp\Collections\NonEmptyHashSet<1|null|2>,
     *     \Fp\Collections\NonEmptyHashMap<'a'|'b'|'c', 1|null|2>,
     *     \Fp\Collections\Seq<1|null|2>,
     *     \Fp\Collections\Set<1|null|2>,
     *     \Fp\Collections\Map<'a'|'b'|'c', 1|null|2>,
     *     \Fp\Collections\NonEmptySeq<1|null|2>,
     *     \Fp\Collections\NonEmptySet<1|null|2>,
     *     \Fp\Collections\NonEmptyMap<'a'|'b'|'c', 1|null|2>,
     *     \Fp\Streams\Stream<1|null|2>,
     * } $in
     * @psalm-return array{
     *     \Fp\Collections\ArrayList<1|2>,
     *     \Fp\Collections\LinkedList<1|2>,
     *     \Fp\Collections\HashSet<1|2>,
     *     \Fp\Collections\HashMap<'a'|'b', 1|2>,
     *     \Fp\Collections\ArrayList<1|2>,
     *     \Fp\Collections\LinkedList<1|2>,
     *     \Fp\Collections\HashSet<1|2>,
     *     \Fp\Collections\HashMap<'a'|'b', 1|2>,
     *     \Fp\Collections\Seq<1|2>,
     *     \Fp\Collections\Set<1|2>,
     *     \Fp\Collections\Map<'a'|'b', 1|2>,
     *     \Fp\Collections\Seq<1|2>,
     *     \Fp\Collections\Set<1|2>,
     *     \Fp\Collections\Map<'a'|'b'|'c', 1|null|2>,
     *     \Fp\Streams\Stream<1|2>,
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
     *     \Fp\Collections\ArrayList<1|null|2>,
     *     \Fp\Collections\LinkedList<1|null|2>,
     *     \Fp\Collections\HashSet<1|null|2>,
     *     \Fp\Collections\NonEmptyArrayList<1|null|2>,
     *     \Fp\Collections\NonEmptyLinkedList<1|null|2>,
     *     \Fp\Collections\NonEmptyHashSet<1|null|2>,
     *     \Fp\Collections\Seq<1|null|2>,
     *     \Fp\Collections\Set<1|null|2>,
     *     \Fp\Collections\NonEmptySeq<1|null|2>,
     *     \Fp\Collections\NonEmptySet<1|null|2>,
     *     \Fp\Streams\Stream<1|null|2>,
     * } $in
     * @psalm-return array{
     *     \Fp\Collections\ArrayList<1|2>,
     *     \Fp\Collections\LinkedList<1|2>,
     *     \Fp\Collections\HashSet<1|2>,
     *     \Fp\Collections\ArrayList<1|2>,
     *     \Fp\Collections\LinkedList<1|2>,
     *     \Fp\Collections\HashSet<1|2>,
     *     \Fp\Collections\Seq<1|2>,
     *     \Fp\Collections\Set<1|2>,
     *     \Fp\Collections\Seq<1|2>,
     *     \Fp\Collections\Set<1|2>,
     *     \Fp\Streams\Stream<1|2>,
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
