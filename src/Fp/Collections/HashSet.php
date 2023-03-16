<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Callable\toSafeClosure;
use function Fp\Cast\asGenerator;
use function Fp\Cast\asList;
use function Fp\Cast\fromPairs;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

/**
 * @template-covariant TV
 * @implements Set<TV>
 *
 * @psalm-seal-methods
 * @mixin HashSetExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class HashSet implements Set
{
    /**
     * @param HashMap<TV, TV> $map
     */
    private function __construct(private readonly HashMap $map) { }

    #region SetCollector

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return HashSet<TVI>
     */
    public static function collect(iterable $source): HashSet
    {
        return new HashSet(ArrayList::collect($source)->map(fn(mixed $elem) => [$elem, $elem])->toHashMap());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return HashSet<TVI>
     */
    public static function singleton(mixed $val): HashSet
    {
        return HashSet::collect([$val]);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<empty>
     */
    public static function empty(): HashSet
    {
        return HashSet::collect([]);
    }

    #endregion SetCollector

    #region SetCastableOps

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        return asList($this->getIterator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<non-empty-list<TV>>
     */
    public function toNonEmptyList(): Option
    {
        return proveNonEmptyList($this->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is HashSet<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return fromPairs($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is HashSet<array{TKO, TVO}>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     */
    public function toNonEmptyArray(): Option
    {
        return proveNonEmptyArray($this->toArray());
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
     * @return Option<NonEmptyLinkedList<TV>>
     */
    public function toNonEmptyLinkedList(): Option
    {
        return NonEmptyLinkedList::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option
    {
        return NonEmptyArrayList::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyHashSet<TV>>
     */
    public function toNonEmptyHashSet(): Option
    {
        return NonEmptyHashSet::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is HashSet<array{TKI, TVI}>
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
     * @psalm-if-this-is HashSet<array{TKI, TVI}>
     *
     * @return Option<NonEmptyHashMap<TKI, TVI>>
     */
    public function toNonEmptyHashMap(): Option
    {
        return NonEmptyHashMap::collectPairs($this);
    }

    /**
     * {{@inheritDoc}}
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
     * @template TArray of array<TKO, TVO>
     * @psalm-if-this-is HashSet<TArray>
     *
     * @return array<TKO, TVO>
     * @psalm-return (TArray is list ? list<TVO> : array<TKO, TVO>)
     */
    public function toMergedArray(): array
    {
        return array_merge(...$this->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @template TArray of array<TKO, TVO>
     * @psalm-if-this-is HashSet<TArray>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     * @psalm-return (TArray is list ? Option<non-empty-list<TVO>> : Option<non-empty-array<TKO, TVO>>)
     */
    public function toNonEmptyMergedArray(): Option
    {
        return proveNonEmptyArray($this->toMergedArray());
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion SetCastableOps

    #region SetChainableOps

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $element
     * @return HashSet<TV|TVI>
     */
    public function appended(mixed $element): HashSet
    {
        return new HashSet($this->map->appended($element, $element));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $that
     * @return HashSet<TV|TVI>
     */
    public function appendedAll(iterable $that): HashSet
    {
        return HashSet::collect(Ops\AppendedAllOperation::of($this)($that));
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     * @return HashSet<TV>
     */
    public function removed(mixed $element): HashSet
    {
        return new HashSet($this->map->removed($element));
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function tail(): HashSet
    {
        return HashSet::collect(Ops\TailOperation::of($this)());
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function init(): HashSet
    {
        return HashSet::collect(Ops\InitOperation::of($this)());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return HashSet<TV>
     */
    public function filter(callable $predicate): HashSet
    {
        return HashSet::collect(Ops\FilterOperation::of($this)(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return HashSet<TV>
     */
    public function filterN(callable $predicate): HashSet
    {
        return $this->filter(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function filterNotNull(): HashSet
    {
        return HashSet::collect(Ops\FilterNotNullOperation::of($this)());
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
        return HashSet::collect(Ops\FilterMapOperation::of($this)(dropFirstArg($callback)));
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
        return $this->filterMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is HashSet<iterable<mixed, TVO>|Collection<mixed, TVO>>
     *
     * @return HashSet<TVO>
     */
    public function flatten(): HashSet
    {
        return HashSet::collect(Ops\FlattenOperation::of($this));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<mixed, TVO>|Collection<mixed, TVO>) $callback
     * @return HashSet<TVO>
     */
    public function flatMap(callable $callback): HashSet
    {
        return HashSet::collect(Ops\FlatMapOperation::of($this)(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): (iterable<mixed, TVO>|Collection<mixed, TVO>) $callback
     * @return HashSet<TVO>
     */
    public function flatMapN(callable $callback): HashSet
    {
        return $this->flatMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return HashSet<TVO>
     */
    public function map(callable $callback): HashSet
    {
        return HashSet::collect(Ops\MapOperation::of($this)(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return HashSet<TVO>
     */
    public function mapN(callable $callback): HashSet
    {
        return $this->map(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return HashSet<TV>
     */
    public function tap(callable $callback): HashSet
    {
        Stream::emits(Ops\TapOperation::of($this)(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return HashSet<TV>
     */
    public function tapN(callable $callback): HashSet
    {
        return $this->tap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return HashSet<TV>
     */
    public function intersect(Set|NonEmptySet $that): HashSet
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ $that($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return HashSet<TV>
     */
    public function diff(Set|NonEmptySet $that): HashSet
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ !$that($elem));
    }

    #endregion SetChainableOps

    #region SetTerminalOps

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this)(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool
    {
        return $this->every(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<HashSet<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this)(dropFirstArg($callback))
            ->map(fn($gen) => HashSet::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<HashSet<TVO>>
     */
    public function traverseOptionN(callable $callback): Option
    {
        return $this->traverseOption(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is HashSet<Option<TVO>>
     *
     * @return Option<HashSet<TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this->getIterator())
            ->map(fn($gen) => HashSet::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, HashSet<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return Ops\TraverseEitherOperation::of($this)(dropFirstArg($callback))
            ->map(fn($gen) => HashSet::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, HashSet<TVO>>
     */
    public function traverseEitherN(callable $callback): Either
    {
        return $this->traverseEither(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, HashSet<TVO>>
     */
    public function traverseEitherMerged(callable $callback): Either
    {
        return Ops\TraverseEitherMergedOperation::of($this)(dropFirstArg($callback))->map(HashSet::collect(...));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, HashSet<TVO>>
     */
    public function traverseEitherMergedN(callable $callback): Either
    {
        return $this->traverseEitherMerged(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is HashSet<Either<E, TVO>>
     *
     * @return Either<E, HashSet<TVO>>
     */
    public function sequenceEither(): Either
    {
        return Ops\TraverseEitherOperation::id($this->getIterator())->map(HashSet::collect(...));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is HashSet<Either<non-empty-list<E>, TVO>>
     *
     * @return Either<non-empty-list<E>, HashSet<TVO>>
     */
    public function sequenceEitherMerged(): Either
    {
        return Ops\TraverseEitherMergedOperation::id($this)->map(HashSet::collect(...));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<HashSet<TV>, HashSet<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return Ops\PartitionOperation::of($this)(dropFirstArg($predicate))
            ->mapLeft(fn($left) => HashSet::collect($left))
            ->map(fn($right) => HashSet::collect($right));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<HashSet<TV>, HashSet<TV>>
     */
    public function partitionN(callable $predicate): Separated
    {
        return $this->partition(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
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
        return Ops\PartitionMapOperation::of($this)(dropFirstArg($callback))
            ->mapLeft(fn($left) => HashSet::collect($left))
            ->map(fn($right) => HashSet::collect($right));
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
        return $this->partitionMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindex(callable $callback): HashMap
    {
        return HashMap::collect(Ops\ReindexOperation::of($this)(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindexN(callable $callback): HashMap
    {
        return $this->reindex(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this)(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool
    {
        return $this->exists(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return HashMap<TKO, NonEmptyHashSet<TV>>
     */
    public function groupBy(callable $callback): HashMap
    {
        return Ops\GroupByOperation::of($this)(dropFirstArg($callback))
            ->map(fn(NonEmptyHashMap $seq) => $seq->values()->toNonEmptyHashSet());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return HashMap<TKO, NonEmptyHashSet<TVO>>
     */
    public function groupMap(callable $group, callable $map): HashMap
    {
        return Ops\GroupMapOperation::of($this)(dropFirstArg($group), dropFirstArg($map))
            ->map(fn(NonEmptyHashMap $hs) => $hs->values()->toNonEmptyHashSet());
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
     * @return HashMap<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): HashMap
    {
        return Ops\GroupMapReduceOperation::of($this)(dropFirstArg($group), dropFirstArg($map), $reduce);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return Ops\FirstOperation::of($this)(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option
    {
        return $this->first(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
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
     * @template TVO
     *
     * @param TVO $init
     * @return Ops\FoldOperation<TV, TVO>
     */
    public function fold(mixed $init): Ops\FoldOperation
    {
        return new Ops\FoldOperation($this->getIterator(), $init);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function head(): Option
    {
        return Ops\HeadOperation::of($this)();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return Ops\LastOperation::of($this)(dropFirstArg($predicate));
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
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option
    {
        return $this->last(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function firstElement(): Option
    {
        return Ops\FirstOperation::of($this)();
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function lastElement(): Option
    {
        return Ops\LastOperation::of($this)();
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->map->get($element)->isSome();
    }

    /**
     * {@inheritDoc}
     */
    public function subsetOf(Set|NonEmptySet $superset): bool
    {
        $isSubset = true;

        foreach ($this as $elem) {
            if (!$superset($elem)) {
                $isSubset = false;
                break;
            }
        }

        return $isSubset;
    }

    public function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this)($start, $sep, $end);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function max(): Option
    {
        return Ops\MaxElementOperation::of($this)();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function maxBy(callable $callback): Option
    {
        return Ops\MaxByElementOperation::of($this)($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function min(): Option
    {
        return Ops\MinElementOperation::of($this)();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function minBy(callable $callback): Option
    {
        return Ops\MinByElementOperation::of($this)($callback);
    }

    #endregion SetTerminalOps

    #region MagicMethods

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->mkString('HashSet(', ', ', ')');
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
        return HashSetExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return HashSetExtensions::callStatic($name, $arguments);
    }

    #endregion MagicMethods

    #region Traversable

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return asGenerator(function () {
            foreach ($this->map as $pair) {
                yield $pair;
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return Ops\CountOperation::of($this)();
    }

    #endregion Traversable
}
