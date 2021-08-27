<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends AbstractMap<TK, TV>
 */
final class HashMap extends AbstractMap
{
    /**
     * @internal
     * @param HashTable<TK, TV> $hashTable
     */
    public function __construct(private HashTable $hashTable)
    {
    }

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collect(iterable $source): self
    {
        $buffer = new HashMapBuffer();

        foreach ($source as [$key, $value]) {
            $buffer->update($key, $value);
        }

        return $buffer->toHashMap();
    }

    /**
     * @psalm-pure
     * @template TKI of array-key
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectIterable(iterable $source): self
    {
        $pairSource = function() use ($source): Generator {
            foreach ($source as $idx => $elem) {
                yield [$idx, $elem];
            }
        };

        return self::collect($pairSource());
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
     * @inheritDoc
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        $elem = null;

        foreach ($this->findBucketByKey($key)->getOrElse([]) as [$k, $v]) {
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
        return self::collect([...$this->toArray(), [$key, $value]]);
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return self<TK, TV>
     */
    public function removed(mixed $key): self
    {
        return $this->filter(fn($v, $k) => $k !== $key);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TK): bool $predicate
     * @psalm-return self<TK, TV>
     */
    public function filter(callable $predicate): self
    {
        $source = function () use ($predicate): Generator {
            foreach ($this->generatePairs() as $pair) {
                if ($predicate($pair[1], $pair[0])) {
                    yield $pair;
                }
            }
        };

        return self::collect($source());
    }

    /**
     * @experimental
     * @psalm-template TKO
     * @psalm-template TVO
     * @psalm-param callable(TV, TK): iterable<array{TKO, TVO}> $callback
     * @psalm-return self<TKO, TVO>
     */
    public function flatMap(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this->generatePairs() as $pair) {
                foreach ($callback($pair[1], $pair[0]) as $p) {
                    yield $p;
                }
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV, TK): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function map(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this->generatePairs() as $pair) {
                yield [$pair[0], $callback($pair[1], $pair[0])];
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV, TK): TKO $callback
     * @psalm-return self<TKO, TV>
     */
    public function reindex(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this->generatePairs() as $pair) {
                yield [$callback($pair[1], $pair[0]), $pair[1]];
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TK>
     */
    public function keys(): Seq
    {
        return ArrayList::collect($this->generateKeys());
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TV>
     */
    public function values(): Seq
    {
        return ArrayList::collect($this->generateValues());
    }

    /**
     * @param TK $key
     * @return Option<list<array{TK, TV}>>
     */
    private function findBucketByKey(mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        return Option::fromNullable($this->hashTable->table[$hash] ?? null);
    }
}
