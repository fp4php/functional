<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Fp\Functional\Option\Option;
use Generator;
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
    private function __construct(private HashMap $hashMap)
    {
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): self
    {
        $isEmpty = true;
        $buffer = new HashMapBuffer();

        foreach ($source as [$key, $value]) {
            $buffer->update($key, $value);
            $isEmpty = false;
        }

        if ($isEmpty) {
            throw new EmptyCollectionException("Non empty collection must contain at least one element");
        }

        return new self($buffer->toHashMap());
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectUnsafe(iterable $source): self
    {
        try {
            return self::collect($source);
        } catch (EmptyCollectionException $e) {
            throw new Error(previous: $e);
        }
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param non-empty-array<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectNonEmpty(iterable $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collectOption(iterable $source): Option
    {
        try {
            return Option::some(self::collect($source));
        } catch (EmptyCollectionException) {
            return Option::none();
        }
    }

    /**
     * @psalm-pure
     * @template TKI of array-key
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     * @throws EmptyCollectionException
     */
    public static function collectIterable(iterable $source): self
    {
        $pairSource = function() use ($source): Generator {
            foreach ($source as $key => $value) {
                yield [$key, $value];
            }
        };

        return self::collect($pairSource());
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI of array-key
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectIterableUnsafe(iterable $source): self
    {
        try {
            return self::collectIterable($source);
        } catch (EmptyCollectionException $e) {
            throw new Error(previous: $e);
        }
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
        return self::collectNonEmpty([...$this->toArray(), [$key, $value]]);
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
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function map(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this->generateEntries() as $entry) {
                yield [$entry->key, $callback($entry)];
                unset($entry);
            }
        };

        return self::collectUnsafe($source());
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return self<TKO, TV>
     */
    public function reindex(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this->generateEntries() as $entry) {
                yield [$callback($entry), $entry->value];
                unset($entry);
            }
        };

        return self::collectUnsafe($source());
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
