<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Operations as Ops;
use Fp\Operations\FoldOperation;
use Fp\Streams\Stream;
use Iterator;
use function Fp\Callable\dropFirstArg;

/**
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 *
 * @psalm-seal-methods
 * @mixin NonEmptyArrayListExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class NonEmptyArrayList implements NonEmptySeq
{
    /**
     * @internal
     * @param ArrayList<TV> $arrayList
     */
    public function __construct(public readonly ArrayList $arrayList)
    {
    }

    #region NonEmptySeqCollector

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
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Option<NonEmptyArrayList<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $collection = ArrayList::collect($source);

        return !$collection->isEmpty()
            ? Option::some(new NonEmptyArrayList($collection))
            : Option::none();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
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
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<mixed, TVI> $source
     * @return NonEmptyArrayList<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($source);
    }

    #endregion NonEmptySeqCollector

    #region NonEmptySeqChainableOps

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>>
     *
     * @return NonEmptyArrayList<TVO>
     */
    public function flatten(): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->flatten());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyArrayList<TVO>
     */
    public function flatMap(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->flatMap($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyArrayList<TVO>
     */
    public function flatMapN(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->flatMapN($callback));
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
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyArrayList<TVO>
     */
    public function map(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->map($callback));
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
        return new NonEmptyArrayList($this->arrayList->mapN($callback));
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
        return new NonEmptyArrayList($this->arrayList->appended($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $suffix
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->appendedAll($suffix));
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
        return new NonEmptyArrayList($this->arrayList->prepended($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $prefix
     * @return NonEmptyArrayList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->prependedAll($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyArrayList<TV>
     */
    public function tap(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->tap($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return NonEmptyArrayList<TV>
     */
    public function tapN(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->tapN($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<mixed, TVI> $that
     * @return NonEmptyArrayList<array{TV, TVI}>
     */
    public function zip(iterable $that): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->zip($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<array{int, TV}>
     */
    public function zipWithKeys(): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->zipWithKeys());
    }

    /**
     * {@inheritDoc}
     *
     * @param null|callable(TV, TV): int $cmp
     * @return NonEmptyArrayList<TV>
     */
    public function sorted(null|callable $cmp = null): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->sorted($cmp));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyArrayList<TV>
     */
    public function sortedBy(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->sortedBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<TV>
     */
    public function sortedDesc(): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->sortedDesc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyArrayList<TV>
     */
    public function sortedDescBy(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->sortedDescBy($callback));
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
        return new NonEmptyArrayList($this->arrayList->intersperse($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyArrayList<TV>
     */
    public function uniqueBy(callable $callback): NonEmptyArrayList
    {
        return new NonEmptyArrayList($this->arrayList->uniqueBy($callback));
    }

    #endregion NonEmptySeqChainableOps

    #region NonEmptySeqTerminalOps

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
     * @param callable(mixed...): bool $predicate
     * @return ArrayList<TV>
     */
    public function filterN(callable $predicate): ArrayList
    {
        return $this->arrayList->filterN($predicate);
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
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return ArrayList<TVO>
     */
    public function filterMapN(callable $callback): ArrayList
    {
        return $this->arrayList->filterMapN($callback);
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
        return $this->arrayList->every($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool
    {
        return $this->arrayList->everyN($predicate);
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
        return $this->arrayList->traverseOption($callback)->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<NonEmptyArrayList<TVO>>
     */
    public function traverseOptionN(callable $callback): Option
    {
        return $this->arrayList->traverseOptionN($callback)->map(fn($list) => new NonEmptyArrayList($list));
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
        return $this->arrayList->sequenceOption()->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, NonEmptyArrayList<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return $this->arrayList->traverseEither($callback)->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, NonEmptyArrayList<TVO>>
     */
    public function traverseEitherN(callable $callback): Either
    {
        return $this->arrayList->traverseEitherN($callback)->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<Either<E, TVO>>
     *
     * @return Either<E, NonEmptyArrayList<TVO>>
     */
    public function sequenceEither(): Either
    {
        return $this->arrayList->sequenceEither()->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<ArrayList<TV>, ArrayList<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return $this->arrayList->partition($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<ArrayList<TV>, ArrayList<TV>>
     */
    public function partitionN(callable $predicate): Separated
    {
        return $this->arrayList->partitionN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<ArrayList<LO>, ArrayList<RO>>
     */
    public function partitionMap(callable $callback): Separated
    {
        return $this->arrayList->partitionMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<ArrayList<LO>, ArrayList<RO>>
     */
    public function partitionMapN(callable $callback): Separated
    {
        return $this->arrayList->partitionMapN($callback);
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
        return new NonEmptyHashMap($this->arrayList->groupMapReduce($group, $map, $reduce));
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
        return new NonEmptyHashMap($this->arrayList->reindex($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return NonEmptyHashMap<TKO, TV>
     */
    public function reindexN(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap($this->arrayList->reindexN($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->arrayList->exists($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool
    {
        return $this->arrayList->existsN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return $this->arrayList->first($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option
    {
        return $this->arrayList->firstN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<TVO>
     */
    public function firstMap(callable $callback): Option
    {
        return Ops\FirstMapOperation::of($this)(dropFirstArg($callback));
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
        return $this->arrayList->last($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option
    {
        return $this->arrayList->lastN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<TVO>
     */
    public function lastMap(callable $callback): Option
    {
        return Ops\LastMapOperation::of($this)(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldOperation<TV, TVO>
     */
    public function fold(mixed $init): FoldOperation
    {
        return new FoldOperation($this->getIterator(), $init);
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
        return $this->arrayList->lastElement()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyHashMap<TKO, NonEmptyArrayList<TV>>
     */
    public function groupBy(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap($this->arrayList->groupBy($callback));
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
        return new NonEmptyHashMap($this->arrayList->groupMap($group, $map));
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return $this->arrayList->mkString($start, $sep, $end);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function max(): mixed
    {
        return $this->arrayList->max()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function maxBy(callable $callback): mixed
    {
        return $this->arrayList->maxBy($callback)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function min(): mixed
    {
        return $this->arrayList->min()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function minBy(callable $callback): mixed
    {
        return $this->arrayList->minBy($callback)->getUnsafe();
    }

    #endregion NonEmptySeqTerminalOps

    #region NonEmptySeqCastableOps

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
        return $this->arrayList->toArray();
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
        return $this->arrayList->toNonEmptyArray()->getUnsafe();
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

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array
    {
        return $this->arrayList->toMergedArray();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyArrayList<array<TKO, TVO>>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyMergedArray(): array
    {
        return $this->arrayList->toNonEmptyMergedArray()->getUnsafe();
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion NonEmptySeqCastableOps

    #region Traversable

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

    #endregion Traversable

    #region Magic methods

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->toArrayList()
            ->mkString('NonEmptyArrayList(', ', ', ')');
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
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return NonEmptyArrayListExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return NonEmptyArrayListExtensions::callStatic($name, $arguments);
    }

    #endregion Magic methods
}
