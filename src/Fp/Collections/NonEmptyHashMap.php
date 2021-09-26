<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Functional\State\State;
use Fp\Functional\State\StateFunctions;
use Iterator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements NonEmptyMap<TK, TV>
 */
final class NonEmptyHashMap implements NonEmptyMap
{
    /**
     * @internal
     * @param HashMap<TK, TV> $hashMap
     */
    public function __construct(private HashMap $hashMap)
    {
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collect(iterable $source): Option
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
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectUnsafe(iterable $source): self
    {
        return self::collect($source)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param non-empty-array<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectNonEmpty(array $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collectPairs(iterable $source): Option
    {
        /**
         * @psalm-var HashTable<TKI, TVI> $init
         */
        $init = new HashTable();

        $hashTable = State::forS($init, function () use ($source) {
            foreach ($source as [$key, $value]) {
                yield StateFunctions::modify(
                    fn(HashTable $tbl) => HashTable::update($tbl, $key, $value)
                );
            }
        });

        $isEmpty = empty($hashTable->table);

        return Option::condLazy(!$isEmpty, fn() => new HashMap($hashTable, $isEmpty))
            ->map(fn(HashMap $map) => new self($map));
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairsUnsafe(iterable $source): self
    {
        return self::collectPairs($source)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param non-empty-array<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairsNonEmpty(array|NonEmptyCollection $source): self
    {
        return self::collectPairsUnsafe($source);
    }

    /**
     * @return Iterator<int, array{TK, TV}>
     */
    public function getIterator(): Iterator
    {
        return $this->hashMap->getIterator();
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
     * @return non-empty-list<array{TK, TV}>
     */
    public function toArray(): array
    {
        return $this->toNonEmptyArrayList()->toArray();
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
     * @return NonEmptyLinkedList<array{TK, TV}>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(asGenerator(function () {
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
     * @return NonEmptyArrayList<array{TK, TV}>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe(asGenerator(function () {
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
     * @return NonEmptyHashSet<array{TK, TV}>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe(asGenerator(function () {
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
        return $this->hashMap;
    }

    /**
     * @return NonEmptyHashMap<TK, TV>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap
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
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        return $this->hashMap->get($key);
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
        return self::collectPairsUnsafe([...$this->toArray(), [$key, $value]]);
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return HashMap<TK, TV>
     */
    public function removed(mixed $key): HashMap
    {
        return $this->hashMap->removed($key);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     * @psalm-return HashMap<TK, TV>
     */
    public function filter(callable $predicate): HashMap
    {
        return $this->hashMap->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(Entry<TK, TV>): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMap(callable $callback): HashMap
    {
        return $this->hashMap->filterMap($callback);
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
        return self::collectPairsUnsafe(asGenerator(function () use ($callback) {
            foreach ($this as [$key, $value]) {
                $entry = new Entry($key, $value);
                yield [$entry->key, $callback($entry)];
                unset($entry);
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return self<TKO, TV>
     */
    public function mapKeys(callable $callback): self
    {
        return self::collectPairsUnsafe(asGenerator(function () use ($callback) {
            foreach ($this as [$key, $value]) {
                $entry = new Entry($key, $value);
                yield [$callback($entry), $entry->value];
                unset($entry);
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptySeq<TK>
     */
    public function keys(): NonEmptySeq
    {
        return NonEmptyArrayList::collectUnsafe(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair[0];
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptySeq<TV>
     */
    public function values(): NonEmptySeq
    {
        return NonEmptyArrayList::collectUnsafe(asGenerator(function () {
            foreach ($this as $pair) {
                yield $pair[1];
            }
        }));
    }
}
