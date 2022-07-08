<?php

declare(strict_types=1);

namespace Fp\Collections;

use Generator;
use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;

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
    public function __construct(private HashTable $hashTable)
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
     * @psalm-if-this-is Map<TKO, TVO>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        /** @var Generator<TKO, TVO> $gen */
        $gen = $this->getKeyValueIterator();

        return asArray($gen);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is Map<TKO, TVO>
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
        return Ops\EveryOperation::of($this->getKeyValueIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option
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
     * @template TA
     *
     * @param TA $init
     * @param callable(TA, TV): TA $callback
     * @return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return Ops\FoldOperation::of($this->getKeyValueIterator())($init, $callback);
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
        return HashMap::collect(Ops\FilterOperation::of($this->getKeyValueIterator())($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TK, TV): bool $predicate
     * @return HashMap<TK, TV>
     */
    public function filterKV(callable $predicate): Map
    {
        return HashMap::collect(Ops\FilterWithKeyOperation::of($this->getKeyValueIterator())($predicate));
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
        return HashMap::collect(Ops\MapOperation::of($this->getKeyValueIterator())($callback));
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
        return HashMap::collect(Ops\MapWithKeyOperation::of($this->getKeyValueIterator())($callback));
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
        return HashMap::collect(Ops\ReindexOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindexKV(callable $callback): Map
    {
        return HashMap::collect(Ops\ReindexWithKeyOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return HashMap<TKO, NonEmptyHashMap<TK, TV>>
     */
    public function groupBy(callable $callback): Map
    {
        return Ops\GroupByOperation::of($this->getKeyValueIterator())($callback);
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

    public function __toString(): string
    {
        return $this
            ->mapKV(fn($key, $value) => Ops\ToStringOperation::of($key) . ' => ' . Ops\ToStringOperation::of($value))
            ->values()
            ->mkString('HashMap(', ', ', ')');
    }
}
