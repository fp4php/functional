<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;
use Iterator;

/**
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 * @implements NonEmptyMap<TK, TV>
 */
abstract class AbstractNonEmptyMap implements NonEmptyMap
{
    /**
     * REPL:
     * >>> NonEmptyHashMap::collect(['a' =>  1, 'b' => 2])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return Option<self<TKI, TVI>>
     */
    abstract public static function collect(iterable $source): Option;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectUnsafe(['a' =>  1, 'b' => 2])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectUnsafe(iterable $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectNonEmpty(['a' =>  1, 'b' => 2])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param non-empty-array<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectNonEmpty(array $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    abstract public static function collectPairs(iterable $source): Option;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectPairsUnsafe(iterable $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @template TKI
     * @template TVI
     * @param non-empty-array<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectPairsNonEmpty(array|NonEmptyCollection $source): self;

    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    abstract public function getIterator(): Iterator;

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
        return LinkedList::collect($this->generatePairs());
    }

    /**
     * @return NonEmptyLinkedList<array{TK, TV}>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this->generatePairs());
    }

    /**
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->generatePairs());
    }

    /**
     * @return NonEmptyArrayList<array{TK, TV}>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this->generatePairs());
    }

    /**
     * @inheritDoc
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->generatePairs());
    }

    /**
     * @return NonEmptyHashSet<array{TK, TV}>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this->generatePairs());
    }

    /**
     * @return HashMap<TK, TV>
     */
    abstract public function toHashMap(): HashMap;

    /**
     * @return NonEmptyHashMap<TK, TV>
     */
    abstract public function toNonEmptyHashMap(): NonEmptyHashMap;

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

        foreach ($this->generateEntries() as $entry) {
            if (!$predicate($entry)) {
                $result = false;
                break;
            }
            unset($entry);
        }

        return $result;
    }

    /**
     * @return Generator<array{TK, TV}>
     */
    protected function generatePairs(): Generator
    {
        foreach ($this as $pair) {
            yield $pair;
        }
    }

    /**
     * @return Generator<Entry<TK, TV>>
     */
    protected function generateEntries(): Generator
    {
        foreach ($this as [$key, $value]) {
            yield new Entry($key, $value);
        }
    }

    /**
     * @return Generator<TK>
     */
    public function generateKeys(): Generator
    {
        foreach ($this as $pair) {
            yield $pair[0];
        }
    }

    /**
     * @return Generator<TV>
     */
    public function generateValues(): Generator
    {
        foreach ($this as $pair) {
            yield $pair[1];
        }
    }
}
