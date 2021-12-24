<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Operations\CountOperation;
use Fp\Operations\EveryMapOperation;
use Fp\Operations\EveryOperation;
use Fp\Operations\FilterMapOperation;
use Fp\Operations\FilterOperation;
use Fp\Operations\FlatMapOperation;
use Fp\Operations\FoldOperation;
use Fp\Operations\KeysOperation;
use Fp\Operations\MapKeysOperation;
use Fp\Operations\MapValuesOperation;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Functional\Option\None;
use Fp\Operations\ValuesOperation;
use Generator;

use function Fp\Cast\asGenerator;
use function Fp\Cast\asList;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
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
            $hashTable = $hashTable->update($hashTable, $key, $value);
        }

        return new HashMap($hashTable);
    }

    /**
     * @return Generator<int, array{TK, TV}>
     */
    public function getIterator(): Generator
    {
        foreach ($this->hashTable->table as $bucket) {
            foreach ($bucket as $pair) {
                yield $pair;
            }
        }
    }

    /**
     * @return Generator<TK, TV>
     */
    public function getKeyValueIterator(): Generator
    {
        foreach ($this as [$key, $value]) {
            yield $key => $value;
        }
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
    public function toArray(): array
    {
        return asList($this->getIterator());
    }

    /**
     * @inheritDoc
     * @psalm-return (TK is array-key ? Some<array<TK, TV>> : None)
     */
    public function toAssocArray(): Option
    {
        $acc = [];

        foreach ($this->getIterator() as [$key, $val]) {
            if (is_object($key) || is_array($key)) {
                return None::getInstance();
            } else {
                $acc[$key] = $val;
            }
        }

        return new Some($acc);
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
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->getIterator());
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
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $predicate(new Entry($key, $value))
        );
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Option<self<TK, TVO>>
     */
    public function everyMap(callable $callback): Option
    {
        $hs = EveryMapOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $callback(new Entry($key, $value))
        );

        return $hs->map(fn($gen) => HashMap::collect($gen));
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
        return self::collectPairs([...$this->toArray(), [$key, $value]]);
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
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function map(callable $callback): self
    {
        return $this->mapValues($callback);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function mapValues(callable $callback): self
    {
        return self::collect(
            MapValuesOperation::of($this->getKeyValueIterator())(
                fn($value, $key) => $callback(new Entry($key, $value))
            )
        );
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return self<TKO, TV>
     */
    public function mapKeys(callable $callback): self
    {
        return self::collect(
            MapKeysOperation::of($this->getKeyValueIterator())(
                fn($value, $key) => $callback(new Entry($key, $value))
            )
        );
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
     * @psalm-suppress ImpureMethodCall
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
