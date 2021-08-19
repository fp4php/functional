<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Fp\Functional\Option\Option;
use Generator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements Map<TK, TV>
 * @psalm-type hash = string
 */
final class HashMap implements Map
{
    /**
     * @var array<hash, Seq<array{TK, TV}>>
     */
    private array $hashTable = [];

    /**
     * @param iterable<array{TK, TV}> $source
     */
    private function __construct(iterable $source)
    {
        foreach ($source as $pair) {

            $hash = (string) $this->computeKeyHash($pair[0]);

            if (!isset($this->hashTable[$hash])) {
                $this->hashTable[$hash] = Nil::getInstance();
            }

            $this->hashTable[$hash] = $this->hashTable[$hash]
                ->filter(fn(array $p) => !$this->keyEquals($pair[0], $p[0]))
                ->prepended($pair);
        }
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
        return new self($source);
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

        /** @psalm-suppress ImpureFunctionCall */
        return self::collect($pairSource());
    }

    /**
     * @return ArrayIterator<int, array{TK, TV}>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * @inheritDoc
     * @return list<array{TK, TV}>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this->generatePairs() as $pair) {
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
        return LinkedList::collect($this->generatePairs());
    }

    /**
     * @inheritDoc
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->toArray());
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
        return $this->findBucketByKey($key)
            ->flatMap(fn(Seq $bucket) => $bucket->first(fn($pair) => $this->keyEquals($pair[0], $key)))
            ->map(fn($pair) => $pair[1]);
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
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this as $pair) {
            if (!$predicate($pair[1], $pair[0])) {
                $result = false;
                break;
            }
        }

        return $result;
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
     * @template TVI
     * @psalm-param TVI $init initial accumulator value
     * @psalm-param callable(TVI, array{TK, TV}): TVI $callback (accumulator, current element): new accumulator
     * @psalm-return TVI
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return $this->toLinkedList()->fold($init, $callback);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(array{TK, TV}, array{TK, TV}): array{TK, TV} $callback (accumulator, current value): new accumulator
     * @psalm-return Option<array{TK, TV}>
     */
    public function reduce(callable $callback): Option
    {
        return $this->toLinkedList()->reduce($callback);
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
        $source = function (): Generator {
            foreach ($this->generatePairs() as $pair) {
                yield $pair[0];
            }
        };

        return LinkedList::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TV>
     */
    public function values(): Seq
    {
        $source = function (): Generator {
            foreach ($this->generatePairs() as $pair) {
                yield $pair[1];
            }
        };

        return LinkedList::collect($source());
    }

    private function keyEquals(mixed $lhs, mixed $rhs): bool
    {
        /** @psalm-suppress ImpureMethodCall */
        return $lhs instanceof HashContract && $rhs instanceof HashContract
            ? $lhs->equals($rhs)
            : $this->keyHashEquals($lhs, $rhs);
    }

    private function keyHashEquals(mixed $lhs, mixed $rhs): bool
    {
        return $this->computeKeyHash($lhs) === $this->computeKeyHash($rhs);
    }

    private function computeKeyHash(mixed $key): mixed
    {
        return match (true) {
            is_object($key) => $this->computeKeyHashForObject($key),
            is_array($key) => $this->computeKeyHashForArray($key),
            default => $key,
        };
    }

    private function computeKeyHashForObject(object $object): string
    {
        /** @psalm-suppress ImpureMethodCall */
        return $object instanceof HashContract
            ? $object->hashCode()
            : spl_object_hash($object);
    }

    private function computeKeyHashForArray(array $arr): string
    {
        $list = LinkedList::collect($arr)
            ->map(fn($elem): mixed => $this->computeKeyHash($elem))
            ->toArray();

        return json_encode($list) ?: '';
    }

    /**
     * @param TK $key
     * @return Option<Seq<array{TK, TV}>>
     */
    private function findBucketByKey(mixed $key): Option
    {
        $hash = (string) $this->computeKeyHash($key);
        return Option::fromNullable($this->hashTable[$hash] ?? null);
    }

    /**
     * @return Generator<array{TK, TV}>
     */
    private function generatePairs(): Generator
    {
        foreach ($this->hashTable as $bucket) {
            foreach ($bucket as $pair) {
                yield $pair;
            }
        }
    }
}
