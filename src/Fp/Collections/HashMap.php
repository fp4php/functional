<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Operations\FoldingOperation;
use Generator;
use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;
use function Fp\Cast\asGenerator;
use function Fp\Cast\asList;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

/**
 * @template TK
 * @template-covariant TV
 * @implements Map<TK, TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class HashMap implements Map
{
    private bool $empty;

    /**
     * @internal
     * @param HashTable<TK, TV> $hashTable
     */
    public function __construct(private readonly HashTable $hashTable)
    {
        $this->empty = empty($hashTable->table);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return HashMap<TKI, TVI>
     */
    public static function collect(iterable $source): self
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
     * @param iterable<array{TKI, TVI}> $source
     * @return HashMap<TKI, TVI>
     */
    public static function collectPairs(iterable $source): self
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

    /**
     * @return Generator<int, array{TK, TV}>
     */
    public function getIterator(): Generator
    {
        return $this->hashTable->getPairsGenerator();
    }

    /**
     * @return Generator<TK, TV>
     */
    public function getKeyValueIterator(): Generator
    {
        return $this->hashTable->getKeyValueIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return Ops\CountOperation::of($this->getIterator())();
    }

    /**
     * {@inheritDoc}
     *
     * @return list<array{TK, TV}>
     */
    public function toList(): array
    {
        return asList($this->getIterator());
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
        return asArray($this->getKeyValueIterator());
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
        return LinkedList::collect($this->getIterator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyLinkedList<array{TK, TV}>>
     */
    public function toNonEmptyLinkedList(): Option
    {
        $list = $this->toLinkedList();

        return $list->head()->map(
            fn($head) => new NonEmptyLinkedList($head, $list->tail()),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->getIterator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyArrayList<array{TK, TV}>>
     */
    public function toNonEmptyArrayList(): Option
    {
        return Option::some($this->toArrayList())
            ->filter(fn($list) => !$list->isEmpty())
            ->map(fn($list) => new NonEmptyArrayList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->getIterator());
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
     * @return Stream<array{TK, TV}>
     */
    public function toStream(): Stream
    {
        return Stream::emits($this->getIterator());
    }

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
     * @param callable(TK, TV): bool $predicate
     */
    public function everyKV(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this->getKeyValueIterator())($predicate);
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
     * @param callable(TK, TV): bool $predicate
     */
    public function existsKV(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this->getKeyValueIterator())($predicate);
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
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Option<HashMap<TK, TVO>>
     */
    public function traverseOptionKV(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getKeyValueIterator())($callback)
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
        $iterator = $this->getKeyValueIterator();

        return Ops\TraverseOptionOperation::id($iterator)
            ->map(fn($gen) => HashMap::collect($gen));
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
        return new FoldingOperation($this->getKeyValueIterator(), $init);
    }

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
    public function updated(mixed $key, mixed $value): self
    {
        return HashMap::collectPairs([...$this->toList(), [$key, $value]]);
    }

    /**
     * {@inheritDoc}
     *
     * @param TK $key
     * @return HashMap<TK, TV>
     */
    public function removed(mixed $key): self
    {
        return $this->filterKV(fn($k) => $k !== $key);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filter(callable $predicate): self
    {
        return $this->filterKV(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filterKV(callable $predicate): HashMap
    {
        return HashMap::collect(Ops\FilterOperation::of($this->getKeyValueIterator())($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMap(callable $callback): self
    {
        return $this->filterMapKV(dropFirstArg($callback));
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
        return HashMap::collect(Ops\FilterMapOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): (iterable<array{TKO, TVO}>) $callback
     * @return HashMap<TKO, TVO>
     */
    public function flatMap(callable $callback): self
    {
        return $this->flatMapKV(dropFirstArg($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): (iterable<array{TKO, TVO}>) $callback
     * @return HashMap<TKO, TVO>
     */
    public function flatMapKV(callable $callback): HashMap
    {
        return HashMap::collectPairs(Ops\FlatMapOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return HashMap<TK, TVO>
     */
    public function map(callable $callback): self
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
    public function mapKV(callable $callback): self
    {
        return HashMap::collect(Ops\MapOperation::of($this->getKeyValueIterator())($callback));
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
            return $callback(...$tuple);
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
     * @param callable(TK, TV): void $callback
     * @return HashMap<TK, TV>
     */
    public function tapKV(callable $callback): HashMap
    {
        Stream::emits(Ops\TapOperation::of($this->getKeyValueIterator())($callback))->drain();
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
    public function reindex(callable $callback): self
    {
        return $this->reindexKV(dropFirstArg($callback));
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
        return HashMap::collect(Ops\ReindexOperation::of($this->getKeyValueIterator())($callback));
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
        return Ops\GroupByOperation::of($this->getKeyValueIterator())($callback);
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
        return Ops\GroupMapOperation::of($this->getKeyValueIterator())($group, $map);
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
        return Ops\GroupMapReduceOperation::of($this->getKeyValueIterator())($group, $map, $reduce);
    }

    /**
     * {@inheritDoc}
     *
     * @return Seq<TK>
     */
    public function keys(): Seq
    {
        return ArrayList::collect(Ops\KeysOperation::of($this->getKeyValueIterator())());
    }

    /**
     * {@inheritDoc}
     *
     * @return Seq<TV>
     */
    public function values(): Seq
    {
        return ArrayList::collect(Ops\ValuesOperation::of($this->getKeyValueIterator())());
    }

    public function isEmpty(): bool
    {
        return $this->empty;
    }

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

    /**
     * {@inheritDoc}
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        $elem = null;
        $hash = (string) HashComparator::computeHash($key);

        $bucket = Option::fromNullable($this->hashTable->table[$hash] ?? null)
            ->getOrElse([]);

        foreach ($bucket as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return $this
            ->mapKV(fn($key, $value) => Ops\ToStringOperation::of($key) . ' => ' . Ops\ToStringOperation::of($value))
            ->values()
            ->mkString('HashMap(', ', ', ')');
    }
}
