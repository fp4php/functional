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
 * @implements NonEmptySet<TV>
 *
 * @psalm-seal-methods
 * @mixin NonEmptyHashSetExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class NonEmptyHashSet implements NonEmptySet
{
    /**
     * @param HashSet<TV> $set
     * @internal
     */
    public function __construct(private readonly HashSet $set) {}

    #region NonEmptySetCollector

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Option<NonEmptyHashSet<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $set = HashSet::collect($source);

        return !$set->isEmpty()
            ? Option::some(new NonEmptyHashSet($set))
            : Option::none();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return NonEmptyHashSet<TVI>
     */
    public static function singleton(mixed $val): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectNonEmpty([$val]);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return NonEmptyHashSet<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyHashSet
    {
        return NonEmptyHashSet::collect($source)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI>|NonEmptyCollection<mixed, TVI> $source
     * @return NonEmptyHashSet<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($source);
    }

    #endregion NonEmptySetCollector

    #region NonEmptySetCastableOps

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        return $this->set->toList();
    }

    /**
     * {@inheritDoc}
     *
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array
    {
        return $this->set->toNonEmptyList()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return $this->set->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<array{TKO, TVO}>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyArray(): array
    {
        return $this->set->toNonEmptyArray()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->set->toLinkedList();
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return $this->set->toArrayList();
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return $this->set->toNonEmptyLinkedList()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return $this->set->toNonEmptyArrayList()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return $this->set;
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyHashSet<array{TKI, TVI}>
     *
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(): HashMap
    {
        return $this->set->toHashMap();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyHashSet<array{TKI, TVI}>
     *
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap
    {
        return $this->set->toNonEmptyHashMap()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return Stream<TV>
     */
    public function toStream(): Stream
    {
        return $this->set->toStream();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array
    {
        return $this->set->toMergedArray();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<array<TKO, TVO>>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyMergedArray(): array
    {
        return $this->set->toNonEmptyMergedArray()->getUnsafe();
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion NonEmptySetCastableOps

    #region NonEmptySetChainableOps

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $element
     * @return NonEmptyHashSet<TV|TVI>
     */
    public function updated(mixed $element): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->appended($element));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>>
     *
     * @return NonEmptyHashSet<TVO>
     */
    public function flatten(): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->flatten());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function map(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->map($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function mapN(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->mapN($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function flatMap(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->flatMap($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function flatMapN(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->flatMapN($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyHashSet<TV>
     */
    public function tap(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->tap($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return NonEmptyHashSet<TV>
     */
    public function tapN(callable $callback): NonEmptyHashSet
    {
        return new NonEmptyHashSet($this->set->tapN($callback));
    }

    #endregion NonEmptySetChainableOps

    #region NonEmptySetTerminalOps

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->set->every($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool
    {
        return $this->set->everyN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptyHashSet<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return $this->set->traverseOption($callback)->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<NonEmptyHashSet<TVO>>
     */
    public function traverseOptionN(callable $callback): Option
    {
        return $this->set->traverseOptionN($callback)->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<Option<TVO>>
     *
     * @return Option<NonEmptyHashSet<TVO>>
     */
    public function sequenceOption(): Option
    {
        return $this->set->sequenceOption()->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, NonEmptyHashSet<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return $this->set->traverseEither($callback)->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, NonEmptyHashSet<TVO>>
     */
    public function traverseEitherN(callable $callback): Either
    {
        return $this->set->traverseEitherN($callback)->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is NonEmptyHashSet<Either<E, TVO>>
     *
     * @return Either<E, NonEmptyHashSet<TVO>>
     */
    public function sequenceEither(): Either
    {
        return $this->set->sequenceEither()->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<HashSet<TV>, HashSet<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return $this->set->partition($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<HashSet<TV>, HashSet<TV>>
     */
    public function partitionN(callable $predicate): Separated
    {
        return $this->set->partitionN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<HashSet<LO>, HashSet<RO>>
     */
    public function partitionMap(callable $callback): Separated
    {
        return $this->set->partitionMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<HashSet<LO>, HashSet<RO>>
     */
    public function partitionMapN(callable $callback): Separated
    {
        return $this->set->partitionMapN($callback);
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
        return new NonEmptyHashMap($this->set->reindex($callback));
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
        return new NonEmptyHashMap($this->set->reindexN($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->set->exists($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool
    {
        return $this->set->existsN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyHashMap<TKO, NonEmptyHashSet<TV>>
     */
    public function groupBy(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap($this->set->groupBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return NonEmptyHashMap<TKO, NonEmptyHashSet<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyHashMap
    {
        return new NonEmptyHashMap($this->set->groupMap($group, $map));
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
        return new NonEmptyHashMap($this->set->groupMapReduce($group, $map, $reduce));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return $this->set->first($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option
    {
        return $this->set->firstN($predicate);
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
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return $this->set->last($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option
    {
        return $this->set->lastN($predicate);
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
        return $this->set->fold($init);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function head(): mixed
    {
        return $this->firstElement();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function firstElement(): mixed
    {
        return $this->set->firstElement()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function lastElement(): mixed
    {
        return $this->set->lastElement()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->set->contains($element);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function tail(): HashSet
    {
        return $this->set->tail();
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function init(): HashSet
    {
        return $this->set->init();
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     * @return HashSet<TV>
     */
    public function removed(mixed $element): HashSet
    {
        return $this->set->removed($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return HashSet<TV>
     */
    public function filter(callable $predicate): HashSet
    {
        return $this->set->filter($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return HashSet<TV>
     */
    public function filterN(callable $predicate): HashSet
    {
        return $this->set->filterN($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return HashSet<TVO>
     */
    public function filterMap(callable $callback): HashSet
    {
        return $this->set->filterMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return HashSet<TVO>
     */
    public function filterMapN(callable $callback): HashSet
    {
        return $this->set->filterMapN($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function filterNotNull(): HashSet
    {
        return $this->set->filterNotNull();
    }

    /**
     * {@inheritDoc}
     */
    public function subsetOf(Set|NonEmptySet $superset): bool
    {
        return $this->set->subsetOf($superset);
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return HashSet<TV>
     */
    public function intersect(Set|NonEmptySet $that): HashSet
    {
        return $this->set->intersect($that);
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return HashSet<TV>
     */
    public function diff(Set|NonEmptySet $that): HashSet
    {
        return $this->set->diff($that);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function max(): mixed
    {
        return $this->set->max()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function maxBy(callable $callback): mixed
    {
        return $this->set->maxBy($callback)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function min(): mixed
    {
        return $this->set->min()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function minBy(callable $callback): mixed
    {
        return $this->set->minBy($callback)->getUnsafe();
    }

    #endregion NonEmptySetTerminalOps

    #region Traversable

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return $this->set->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->set->count();
    }

    #endregion Traversable

    #region Magic methods

    public function __toString(): string
    {
        return $this
            ->map(Ops\ToStringOperation::of(...))
            ->toArrayList()
            ->mkString('NonEmptyHashSet(', ', ', ')');
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return NonEmptyHashSetExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return NonEmptyHashSetExtensions::callStatic($name, $arguments);
    }

    #endregion Magic methods
}
