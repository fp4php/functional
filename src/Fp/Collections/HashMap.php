<?php

declare(strict_types=1);

namespace Fp\Collections;

use Generator;
use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use Fp\Streams\Stream;

use function Fp\Callable\toSafeClosure;
use function Fp\Cast\asList;
use function Fp\Cast\asArray;
use function Fp\Cast\asGenerator;
use function Fp\Evidence\proveNonEmptyList;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Callable\dropFirstArg;

/**
 * @template TK
 * @template-covariant TV
 * @implements Map<TK, TV>
 *
 * @psalm-seal-methods
 * @mixin HashMapExtensions<TK, TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class HashMap implements Map
{
    /**
     * @param HashTable<TK, TV> $hashTable
     */
    public function __construct(private readonly HashTable $hashTable)
    {
    }

    #region MapCollectorOps

    /**
     * @return HashMap<empty, empty>
     */
    public static function empty(): HashMap
    {
        return HashMap::collect([]);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> | Collection<TKI, TVI> $source
     * @return HashMap<TKI, TVI>
     */
    public static function collect(iterable $source): HashMap
    {
        $gen = asGenerator(function() use ($source) {
            foreach ($source as $key => $value) {
                yield [$key, $value];
            }
        });

        return HashMap::collectPairs($gen);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     *
     * @param (iterable<mixed, array{TKI, TVI}>|Collection<mixed, array{TKI, TVI}>) $source
     * @return HashMap<TKI, TVI>
     */
    public static function collectPairs(iterable $source): HashMap
    {
        /**
         * @psalm-var HashTable<TKI, TVI> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($source as [$key, $value]) {
            $hashTable->update($key, $value);
        }

        return new HashMap($hashTable);
    }

    #endregion MapCollectorOps

    #region MapCastableOps

    /**
     * {@inheritDoc}
     *
     * @return list<array{TK, TV}>
     */
    public function toList(): array
    {
        return asList($this->hashTable->getPairsGenerator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<non-empty-list<array{TK, TV}>>
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
     * @psalm-if-this-is HashMap<TKO, TVO>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return asArray($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is HashMap<TKO, TVO>
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
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this->hashTable->getPairsGenerator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyLinkedList<array{TK, TV}>>
     */
    public function toNonEmptyLinkedList(): Option
    {
        return NonEmptyLinkedList::collect($this->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->hashTable->getPairsGenerator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyArrayList<array{TK, TV}>>
     */
    public function toNonEmptyArrayList(): Option
    {
        return Option::some($this->toArrayList())->flatMap(
            fn($list) => !$list->isEmpty()
                ? Option::some(new NonEmptyArrayList($list))
                : Option::none(),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->hashTable->getPairsGenerator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyHashSet<array{TK, TV}>>
     */
    public function toNonEmptyHashSet(): Option
    {
        return Option::some($this->toHashSet())
            ->filter(fn($set) => !$set->isEmpty())
            ->map(fn($set) => new NonEmptyHashSet($set));
    }

    /**
     * {@inheritDoc}
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyHashMap<TK, TV>>
     */
    public function toNonEmptyHashMap(): Option
    {
        return Option::some($this)
            ->filter(fn($map) => !$map->isEmpty())
            ->map(fn($map) => new NonEmptyHashMap($map));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is HashMap<TK, array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array
    {
        return array_merge(...$this->values()->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is HashMap<TK, array<TKO, TVO>>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     */
    public function toNonEmptyMergedArray(): Option
    {
        return proveNonEmptyArray($this->toMergedArray());
    }

    /**
     * {@inheritDoc}
     *
     * @return Stream<array{TK, TV}>
     */
    public function toStream(): Stream
    {
        return Stream::emits($this->hashTable->getPairsGenerator());
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion MapCastableOps

    #region MapChainableOps

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     *
     * @param TKI $key
     * @param TVI $value
     * @return HashMap<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): HashMap
    {
        return HashMap::collectPairs([...$this->toList(), [$key, $value]]);
    }

    /**
     * {@inheritDoc}
     *
     * @param TK $key
     * @return HashMap<TK, TV>
     */
    public function removed(mixed $key): HashMap
    {
        return $this->filterKV(fn($k) => $k !== $key);
    }

    /**
     * @template TKO
     * @template TVO
     *
     * @param Map<TKO, TVO>|NonEmptyMap<TKO, TVO>|iterable<TKO, TVO> $map
     * @return HashMap<TK|TKO, TV|TVO>
     */
    public function merge(iterable $map): HashMap
    {
        return HashMap::collect(Ops\MergeMapOperation::of($this)($map));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filter(callable $predicate): HashMap
    {
        return $this->filterKV(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filterN(callable $predicate): HashMap
    {
        return $this->filterKV(function($_k, $tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filterKV(callable $predicate): HashMap
    {
        return HashMap::collect(Ops\FilterOperation::of($this)($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMap(callable $callback): HashMap
    {
        return $this->filterMapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMapN(callable $callback): HashMap
    {
        return $this->filterMapKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMapKV(callable $callback): HashMap
    {
        return HashMap::collect(Ops\FilterMapOperation::of($this)($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     * @psalm-if-this-is HashMap<TK, iterable<TKO, TVO>|Collection<TKO, TVO>>
     *
     * @return HashMap<TKO, TVO>
     */
    public function flatten(): HashMap
    {
        return HashMap::collect(Ops\FlattenOperation::of($this));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): (iterable<TKO, TVO>|Collection<TKO, TVO>) $callback
     * @return HashMap<TKO, TVO>
     */
    public function flatMap(callable $callback): HashMap
    {
        return $this->flatMapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(mixed...): (iterable<TKO, TVO>|Collection<TKO, TVO>) $callback
     * @return HashMap<TKO, TVO>
     */
    public function flatMapN(callable $callback): HashMap
    {
        return $this->flatMapKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): (iterable<TKO, TVO>|Collection<TKO, TVO>) $callback
     * @return HashMap<TKO, TVO>
     */
    public function flatMapKV(callable $callback): HashMap
    {
        return HashMap::collect(Ops\FlatMapOperation::of($this)($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return HashMap<TK, TVO>
     */
    public function map(callable $callback): HashMap
    {
        return $this->mapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TK, TV): TVO $callback
     * @return HashMap<TK, TVO>
     */
    public function mapKV(callable $callback): HashMap
    {
        return HashMap::collect(Ops\MapOperation::of($this)($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return HashMap<TK, TVO>
     */
    public function mapN(callable $callback): HashMap
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
     * @return HashMap<TK, TV>
     */
    public function tap(callable $callback): HashMap
    {
        return $this->tapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return HashMap<TK, TV>
     */
    public function tapN(callable $callback): HashMap
    {
        return $this->tapKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): void $callback
     * @return HashMap<TK, TV>
     */
    public function tapKV(callable $callback): HashMap
    {
        Stream::emits(Ops\TapOperation::of($this)($callback))->drain();
        return $this;
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
        return $this->reindexKV(dropFirstArg($callback));
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
        return $this->reindexKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindexKV(callable $callback): HashMap
    {
        return HashMap::collect(Ops\ReindexOperation::of($this)($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return HashMap<TKO, NonEmptyHashMap<TK, TV>>
     */
    public function groupBy(callable $callback): HashMap
    {
        return $this->groupByKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return HashMap<TKO, NonEmptyHashMap<TK, TV>>
     */
    public function groupByKV(callable $callback): HashMap
    {
        return Ops\GroupByOperation::of($this)($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return HashMap<TKO, NonEmptyHashMap<TK, TVO>>
     */
    public function groupMap(callable $group, callable $map): HashMap
    {
        return $this->groupMapKV(dropFirstArg($group), dropFirstArg($map));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): TKO $group
     * @param callable(TK, TV): TVO $map
     * @return HashMap<TKO, NonEmptyHashMap<TK, TVO>>
     */
    public function groupMapKV(callable $group, callable $map): HashMap
    {
        return Ops\GroupMapOperation::of($this)($group, $map);
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
        return $this->groupMapReduceKV(dropFirstArg($group), dropFirstArg($map), $reduce);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): TKO $group
     * @param callable(TK, TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return HashMap<TKO, TVO>
     */
    public function groupMapReduceKV(callable $group, callable $map, callable $reduce): HashMap
    {
        return Ops\GroupMapReduceOperation::of($this)($group, $map, $reduce);
    }

    #endregion MapChainableOps

    #region MapTerminalOps

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->everyKV(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool
    {
        return $this->everyKV(function($_k, $tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function everyKV(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this)($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-assert-if-true HashMap<TK, TVO> $this
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function everyOf(string|array $fqcn, bool $invariant = false): bool
    {
        return Ops\EveryOfOperation::of($this)($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->existsKV(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool
    {
        return $this->existsKV(function($_k, $tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function existsKV(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this)($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<HashMap<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return $this->traverseOptionKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<HashMap<TK, TVO>>
     */
    public function traverseOptionN(callable $callback): Option
    {
        return $this->traverseOptionKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Option<HashMap<TK, TVO>>
     */
    public function traverseOptionKV(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this)($callback)
            ->map(fn($gen) => HashMap::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is HashMap<TK, Option<TVO>>
     *
     * @return Option<HashMap<TK, TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this)->map(fn($gen) => HashMap::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, HashMap<TK, TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return $this->traverseEitherKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, HashMap<TK, TVO>>
     */
    public function traverseEitherN(callable $callback): Either
    {
        return $this->traverseEitherKV(function($_k, $tuple) use ($callback) {
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
     * @param callable(TK, TV): Either<E, TVO> $callback
     * @return Either<E, HashMap<TK, TVO>>
     */
    public function traverseEitherKV(callable $callback): Either
    {
        return Ops\TraverseEitherOperation::of($this)($callback)->map(fn($gen) => HashMap::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is HashMap<TK, Either<E, TVO>>
     *
     * @return Either<E, HashMap<TK, TVO>>
     */
    public function sequenceEither(): Either
    {
        return Ops\TraverseEitherOperation::id($this)->map(fn($gen) => HashMap::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<HashMap<TK, TV>, HashMap<TK, TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return $this->partitionKV(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<HashMap<TK, TV>, HashMap<TK, TV>>
     */
    public function partitionN(callable $predicate): Separated
    {
        return $this->partitionKV(function($_k, $tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     * @return Separated<HashMap<TK, TV>, HashMap<TK, TV>>
     */
    public function partitionKV(callable $predicate): Separated
    {
        return Ops\PartitionOperation::of($this)($predicate)
            ->mapLeft(fn($left) => HashMap::collect($left))
            ->map(fn($right) => HashMap::collect($right));
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<HashMap<TK, LO>, HashMap<TK, RO>>
     */
    public function partitionMap(callable $callback): Separated
    {
        return $this->partitionMapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<HashMap<TK, LO>, HashMap<TK, RO>>
     */
    public function partitionMapN(callable $callback): Separated
    {
        return $this->partitionMapKV(function($_k, $tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TK, TV): Either<LO, RO> $callback
     * @return Separated<HashMap<TK, LO>, HashMap<TK, RO>>
     */
    public function partitionMapKV(callable $callback): Separated
    {
        return Ops\PartitionMapOperation::of($this)($callback)
            ->mapLeft(fn($left) => HashMap::collect($left))
            ->map(fn($right) => HashMap::collect($right));
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
        return new Ops\FoldOperation($this, $init);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TK>
     */
    public function keys(): ArrayList
    {
        return ArrayList::collect(Ops\KeysOperation::of($this)());
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function values(): ArrayList
    {
        return ArrayList::collect(Ops\ValuesOperation::of($this)());
    }

    public function isEmpty(): bool
    {
        return $this->hashTable->isEmpty();
    }

    /**
     * {@inheritDoc}
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        return $this->hashTable->get($key);
    }

    #endregion MapTerminalOps

    #region Traversable

    /**
     * @return Generator<TK, TV>
     */
    public function getIterator(): Generator
    {
        return $this->hashTable->getKeyValueIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return Ops\CountOperation::of($this)();
    }

    #endregion Traversable

    #region Magic methods

    /**
     * {@inheritDoc}
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option
    {
        return $this->get($key);
    }

    public function __toString(): string
    {
        return $this
            ->mapKV(fn($key, $value) => Ops\ToStringOperation::of($key) . ' => ' . Ops\ToStringOperation::of($value))
            ->values()
            ->mkString('HashMap(', ', ', ')');
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return HashMapExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return HashMapExtensions::callStatic($name, $arguments);
    }

    #endregion Magic methods
}
