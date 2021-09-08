<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends AbstractNonEmptyMap<TK, TV>
 */
final class NonEmptyHashMap extends AbstractNonEmptyMap
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
        return self::collectPairs(PureIterable::of(function () use ($source) {
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
     * @param iterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collectPairs(iterable $source): Option
    {
        $buffer = new HashMapBuffer();

        foreach ($source as [$key, $value]) {
            $buffer->update($key, $value);
        }

        $hashMap = $buffer->toHashMap();

        return Option::cond(!$hashMap->isEmpty(), new self($hashMap));
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
        return self::collectPairsUnsafe(PureIterable::of(function () use ($callback) {
            foreach ($this->generateEntries() as $entry) {
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
        return self::collectPairsUnsafe(PureIterable::of(function () use ($callback) {
            foreach ($this->generateEntries() as $entry) {
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
        return NonEmptyArrayList::collectUnsafe($this->generateKeys());
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptySeq<TV>
     */
    public function values(): NonEmptySeq
    {
        return NonEmptyArrayList::collectUnsafe($this->generateValues());
    }
}
