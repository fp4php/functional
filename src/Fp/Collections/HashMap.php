<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Collections\Operations\MapKeysOperation;
use Fp\Collections\Operations\MapValuesOperation;
use Fp\Functional\Option\Option;
use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements Map<TK, TV>
 * @implements StaticStorage<empty>
 */
final class HashMap implements Map, StaticStorage
{
    /**
     * @internal
     * @psalm-param HashTable<TK, TV> $hashTable
     */
    public function __construct(private HashTable $hashTable, private bool $empty) { }

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
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairs(iterable $source): self
    {
        $buffer = new HashMapBuffer();

        foreach ($source as [$key, $value]) {
            $buffer->update($key, $value);
        }

        return $buffer->toHashMap();
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
        foreach ($this->hashTable->table as $bucket) {
            foreach ($bucket as [$key, $value]) {
                yield $key => $value;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $counter = 0;

        foreach ($this as $ignored) {
            $counter++;
        }

        return $counter;
    }

    /**
     * @inheritDoc
     * @return list<array{TK, TV}>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this as $pair) {
            $buffer[] = $pair;
        }

        return $buffer;
    }

    /**
     * @inheritDoc
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair;
            }
        }));
    }

    /**
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair;
            }
        }));
    }

    /**
     * @inheritDoc
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair;
            }
        }));
    }

    /**
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return $this;
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
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this as [$key, $value]) {
            $entry = new Entry($key, $value);

            if (!$predicate($entry)) {
                $result = false;
                break;
            }

            unset($entry);
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, Entry<TK, TV>): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        $acc = $init;

        foreach ($this as [$key, $value]) {
            $entry = new Entry($key, $value);
            $acc = $callback($acc, $entry);
            unset($entry);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        $elem = null;

        $bucket = $this->findBucketByKey($key)->getOrElse([]);

        foreach ($bucket as [$k, $v]) {
            /** @psalm-suppress ImpureMethodCall */
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
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
        return self::collectPairs(asGenerator(function () use ($predicate) {
            foreach ($this as [$key, $value]) {
                $entry = new Entry($key, $value);

                if ($predicate($entry)) {
                    yield $entry->toArray();
                }

                unset($entry);
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(Entry<TK, TV>): Option<TVO> $callback
     * @return self<TK, TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collectPairs(asGenerator(function () use ($callback) {
            foreach ($this as [$key, $value]) {
                $entry = new Entry($key, $value);
                $result = $callback($entry);

                if ($result->isSome()) {
                    yield [$entry->key, $result->get()];
                }

                unset($entry);
            }
        }));
    }

    /**
     * @experimental
     * @psalm-template TKO
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): iterable<array{TKO, TVO}> $callback
     * @psalm-return self<TKO, TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collectPairs(asGenerator(function () use ($callback) {
            foreach ($this as [$key, $value]) {
                $entry = new Entry($key, $value);

                foreach ($callback($entry) as $p) {
                    yield $p;
                }

                unset($entry);
            }
        }));
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
        return ArrayList::collect(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair[0];
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TV>
     */
    public function values(): Seq
    {
        return ArrayList::collect(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair[1];
            }
        }));
    }

    public function isEmpty():bool
    {
        return $this->empty;
    }

    /**
     * @param TK $key
     * @return Option<list<array{TK, TV}>>
     * @psalm-suppress ImpureMethodCall
     */
    private function findBucketByKey(mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        return Option::fromNullable($this->hashTable->table[$hash] ?? null);
    }
}
