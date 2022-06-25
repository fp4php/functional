<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Operations\CountOperation;
use Fp\Operations\MapWithKeyOperation;
use Fp\Operations\MapOperation;
use Fp\Operations\ReindexOperation;
use Fp\Operations\TraverseOptionOperation;
use Fp\Operations\EveryOperation;
use Fp\Operations\FilterMapOperation;
use Fp\Operations\FilterOperation;
use Fp\Operations\FlatMapOperation;
use Fp\Operations\FoldOperation;
use Fp\Operations\KeysOperation;
use Fp\Operations\ReindexWithKeyOperation;
use Fp\Functional\Option\Option;
use Fp\Operations\ValuesOperation;
use Generator;
use RuntimeException;

use function Fp\Cast\asGenerator;
use function Fp\Cast\asList;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-suppress InvalidTemplateParam
 * @implements Map<TK, TV>
 * @implements StaticStorage<empty>
 */
final class HashMap implements Map, StaticStorage
{
    private bool $empty;

    /**
     * @internal
     * @psalm-param HashTable<TK, TV> $hashTable
     */
    public function __construct(private HashTable $hashTable)
    {
        $this->empty = empty($hashTable->table);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collect(iterable $source): self
    {
        return self::collectPairs(asGenerator(function () use ($source) {
            foreach ($source as $key => $value) {
                yield [$key, $value];
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
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
     * @inheritDoc
     */
    public function count(): int
    {
        return CountOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @return list<array{TK, TV}>
     */
    public function toList(): array
    {
        return asList($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return Option<non-empty-list<array{TK, TV}>>
     */
    public function toNonEmptyList(): Option
    {
        return proveNonEmptyList($this->toList());
    }

    /**
     * @inheritDoc
     * @psalm-return (TK is array-key ? array<TK, TV> : never)
     */
    public function toArray(): array
    {
        $acc = [];

        foreach ($this->getIterator() as [$key, $val]) {
            if (is_object($key) || is_array($key)) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException('HashMap cannot be represented as array<TK, TV>');
                // @codeCoverageIgnoreEnd
            } else {
                $acc[$key] = $val;
            }
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @psalm-return (TK is array-key ? Option<non-empty-array<TK, TV>> : never)
     * @psalm-suppress MixedInferredReturnType
     */
    public function toNonEmptyArray(): Option
    {
        /** @psalm-suppress NoValue */
        $assoc = $this->toArray();

        /** @psalm-suppress UnevaluatedCode */
        return proveNonEmptyArray($assoc);
    }

    /**
     * @inheritDoc
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyLinkedList<array{TK, TV}>>
     */
    public function toNonEmptyLinkedList(): Option
    {
        $linkedList = $this->toLinkedList();

        return Option::when(
            !$linkedList->isEmpty(),
            fn() => new NonEmptyLinkedList($linkedList->head()->getUnsafe(), $linkedList->tail()),
        );
    }

    /**
     * @inheritDoc
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyArrayList<array{TK, TV}>>
     */
    public function toNonEmptyArrayList(): Option
    {
        $arrayList = $this->toArrayList();

        return Option::when(
            !$arrayList->isEmpty(),
            fn() => new NonEmptyArrayList($arrayList),
        );
    }

    /**
     * @inheritDoc
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyHashSet<array{TK, TV}>>
     */
    public function toNonEmptyHashSet(): Option
    {
        $hashSet = $this->toHashSet();

        return Option::when(
            !$hashSet->isEmpty(),
            fn() => new NonEmptyHashSet($hashSet),
        );
    }

    /**
     * @inheritDoc
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyHashMap<TK, TV>>
     */
    public function toNonEmptyHashMap(): Option
    {
        return Option::when(
            !$this->isEmpty(),
            fn() => new NonEmptyHashMap($this),
        );
    }

    /**
     * @inheritDoc
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getKeyValueIterator())($predicate);
    }

    /**
     * @inheritDoc
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<self<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return TraverseOptionOperation::of($this->getKeyValueIterator())($callback)
            ->map(fn($gen) => HashMap::collect($gen));
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param TA $init
     * @psalm-param callable(TA, Entry<TK, TV>): TA $callback
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return FoldOperation::of($this->getKeyValueIterator())(
            $init,
            function (mixed $acc, $value, $key) use ($callback) {
                /** @psalm-var TA $acc */
                return $callback($acc, new Entry($key, $value));
            }
        );
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param TKI $key
     * @param TVI $value
     * @return self<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): self
    {
        return self::collectPairs([...$this->toList(), [$key, $value]]);
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return self<TK, TV>
     */
    public function removed(mixed $key): self
    {
        return $this->filter(fn(Entry $e) => $e->key !== $key);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     * @psalm-return self<TK, TV>
     */
    public function filter(callable $predicate): self
    {
        return self::collect(FilterOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $predicate(new Entry($key, $value))
        ));
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(Entry<TK, TV>): Option<TVO> $callback
     * @return self<TK, TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(FilterMapOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $callback(new Entry($key, $value))
        ));
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-template TKO
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): iterable<array{TKO, TVO}> $callback
     * @psalm-return self<TKO, TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collectPairs(
            FlatMapOperation::of($this->getKeyValueIterator())(
                fn($value, $key) => $callback(new Entry($key, $value))
            )
        );
    }

    /**
     * @inheritDoc
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return self<TK, TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * @inheritDoc
     *
     * @template TVO
     *
     * @param callable(TK, TV): TVO $callback
     * @return self<TK, TVO>
     */
    public function mapWithKey(callable $callback): self
    {
        return self::collect(MapWithKeyOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * @inheritDoc
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return self<TKO, TV>
     */
    public function reindex(callable $callback): self
    {
        return self::collect(ReindexOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * @inheritDoc
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return self<TKO, TV>
     */
    public function reindexWithKey(callable $callback): Map
    {
        return self::collect(ReindexWithKeyOperation::of($this->getKeyValueIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TK>
     */
    public function keys(): Seq
    {
        return ArrayList::collect(KeysOperation::of($this->getKeyValueIterator())());
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TV>
     */
    public function values(): Seq
    {
        return ArrayList::collect(ValuesOperation::of($this->getKeyValueIterator())());
    }

    public function isEmpty():bool
    {
        return $this->empty;
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc
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
}
