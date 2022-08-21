<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations as Ops;
use Fp\Operations\FoldingOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\fromPairs;
use function Fp\Collection\keys;

/**
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class NonEmptyArrayList implements NonEmptySeq
{
    /**
     * @internal
     * @param ArrayList<TV> $arrayList
     */
    public function __construct(public ArrayList $arrayList)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return NonEmptyArrayList<TVI>
     */
    public static function singleton(mixed $val): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectNonEmpty([$val]);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return Option<NonEmptyArrayList<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return Option::some(ArrayList::collect($source))
            ->filter(fn($list) => !$list->isEmpty())
            ->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return NonEmptyArrayList<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyArrayList
    {
        return NonEmptyArrayList::collect($source)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<TVI> $source
     * @return NonEmptyArrayList<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($source);
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return $this->arrayList->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->arrayList->count();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function filter(callable $predicate): ArrayList
    {
        return $this->arrayList->filter($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return ArrayList<TVO>
     */
    public function filterMap(callable $callback): ArrayList
    {
        return $this->arrayList->filterMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function filterNotNull(): ArrayList
    {
        return $this->arrayList->filterNotNull();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return ArrayList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): ArrayList
    {
        return $this->arrayList->filterOf($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<TVO>) $callback
     * @return ArrayList<TVO>
     */
    public function flatMap(callable $callback): ArrayList
    {
        return $this->arrayList->flatMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function tail(): ArrayList
    {
        return $this->arrayList->tail();
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function init(): ArrayList
    {
        return $this->arrayList->init();
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<TV>
     */
    public function reverse(): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->reverse());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function takeWhile(callable $predicate): ArrayList
    {
        return $this->arrayList->takeWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function dropWhile(callable $predicate): ArrayList
    {
        return $this->arrayList->dropWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function take(int $length): ArrayList
    {
        return $this->arrayList->take($length);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function drop(int $length): ArrayList
    {
        return $this->arrayList->drop($length);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyArrayList<TVO>
     */
    public function map(callable $callback): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\MapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return NonEmptyArrayList<TVO>
     */
    public function mapN(callable $callback): NonEmptyArrayList
    {
        return $this->map(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return $callback(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $suffix
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $prefix
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyArrayList<TV>
     */
    public function tap(callable $callback): NonEmptyArrayList
    {
        Stream::emits(Ops\TapOperation::of($this->getIterator())(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<TVI> | NonEmptyCollection<TVI> $that
     * @return NonEmptyArrayList<array{TV, TVI}>
     */
    public function zip(iterable $that): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\ZipOperation::of($this->getIterator())($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<array{int, TV}>
     */
    public function zipWithKeys(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\ZipOperation::of(keys($this->getIterator()))($this->getIterator()));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV, TV): int $cmp
     * @return NonEmptyArrayList<TV>
     */
    public function sorted(callable $cmp): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function at(int $index): Option
    {
        return $this->arrayList->at($index);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return Ops\EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptyArrayList<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => NonEmptyArrayList::collectUnsafe($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<Option<TVO>>
     *
     * @return Option<NonEmptyArrayList<TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this->getIterator())
            ->map(fn($gen) => NonEmptyArrayList::collectUnsafe($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return NonEmptyHashMap<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): NonEmptyHashMap
    {
        return new NonEmptyHashMap(Ops\GroupMapReduceOperation::of($this->getIterator())(dropFirstArg($group), dropFirstArg($map), $reduce));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyHashMap<TKO, TV>
     */
    public function reindex(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap(
            HashMap::collect(Ops\ReindexOperation::of($this->getIterator())(dropFirstArg($callback))),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return Ops\ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return Ops\FirstOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return Ops\FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return Ops\LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function head(): mixed
    {
        return $this->arrayList->head()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return Ops\LastOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldingOperation<TV, TVO>
     */
    public function fold(mixed $init): FoldingOperation
    {
        return new FoldingOperation($this->getIterator(), $init);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function lastElement(): mixed
    {
        return Ops\LastOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptyArrayList<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap
    {
        $groups = Ops\GroupByOperation::of($this)(dropFirstArg($callback));

        return (new NonEmptyHashMap($groups))
            ->map(fn(NonEmptyHashMap $elem) => $elem->values()->toNonEmptyArrayList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return NonEmptyMap<TKO, NonEmptyArrayList<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap
    {
        $groups = Ops\GroupMapOperation::of($this)(dropFirstArg($group), dropFirstArg($map));

        return (new NonEmptyHashMap($groups))
            ->map(fn(NonEmptyHashMap $elem) => $elem->values()->toNonEmptyArrayList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return NonEmptyArrayList<TV | TVI>
     */
    public function intersperse(mixed $separator): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(Ops\IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        /** @var non-empty-list<TV> */
        return $this->arrayList->elements;
    }

    /**
     * {@inheritDoc}
     *
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array
    {
        /** @var non-empty-list<TV> */
        return $this->arrayList->elements;
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return fromPairs($this);
    }

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toNonEmptyArray();
     * => ['fst' => 1, 'snd' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<array{TKO, TVO}>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyArray(): array
    {
        /** @var non-empty-array<TKO, TVO> */
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return $this->arrayList;
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyArrayList<array{TKI, TVI}>
     *
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(): HashMap
    {
        return HashMap::collectPairs($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyArrayList<array{TKI, TVI}>
     *
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsNonEmpty($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return Stream<TV>
     */
    public function toStream(): Stream
    {
        return Stream::emits($this);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->toArrayList()
            ->mkString('NonEmptyArrayList(', ', ', ')');
    }
}
