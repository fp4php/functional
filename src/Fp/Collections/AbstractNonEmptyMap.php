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
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param array<array{TKI, TVI}>|Collection<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}>|PureIterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     * @throws EmptyCollectionException
     */
    abstract public static function collect(array|Collection|NonEmptyCollection|PureIterable $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectUnsafe([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param array<array{TKI, TVI}>|Collection<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}>|PureIterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectUnsafe(array|Collection|NonEmptyCollection|PureIterable $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectNonEmpty([['a', 1], ['b', 2]])
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param non-empty-array<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}>|PureIterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    abstract public static function collectNonEmpty(array|NonEmptyCollection|PureIterable $source): self;

    /**
     * REPL:
     * >>> NonEmptyHashMap::collectOption([['a', 1], ['b', 2]])->get()
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     *
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param array<array{TKI, TVI}>|Collection<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}>|PureIterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    abstract public static function collectOption(array|Collection|NonEmptyCollection|PureIterable $source): Option;

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
     * @return PureIterable<array{TK, TV}>
     */
    protected function generatePairs(): PureIterable
    {
        return PureIterable::of(function () {
            foreach ($this as $pair) {
                yield $pair;
            }
        });
    }

    /**
     * @return PureIterable<Entry<TK, TV>>
     */
    protected function generateEntries(): PureIterable
    {
        return PureIterable::of(function () {
            foreach ($this as [$key, $value]) {
                yield new Entry($key, $value);
            }
        });
    }

    /**
     * @return PureIterable<TK>
     */
    public function generateKeys(): PureIterable
    {
        return PureIterable::of(function () {
            foreach ($this as $pair) {
                yield $pair[0];
            }
        });
    }

    /**
     * @return PureIterable<TV>
     */
    public function generateValues(): PureIterable
    {
        return PureIterable::of(function () {
            $i = 0;

            foreach ($this as $pair) {
                yield $i++ => $pair[1];
            }
        });
    }
}
