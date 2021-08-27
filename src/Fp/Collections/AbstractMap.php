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
 * @implements Map<TK, TV>
 */
abstract class AbstractMap implements Map
{
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
        return LinkedList::collect($this->generatePairs());
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
     * @psalm-param callable(TV, TK): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this as [$key, $value]) {
            if (!$predicate($value, $key)) {
                $result = false;
                break;
            }
        }

        return $result;
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
        $acc = $init;

        foreach ($this as $pair) {
            $acc = $callback($acc, $pair);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @exprimental
     * @psalm-param callable(array{TK, TV}, array{TK, TV}): array{TK, TV} $callback (accumulator, current value): new accumulator
     * @psalm-return Option<array{TK, TV}>
     */
    public function reduce(callable $callback): Option
    {
        return $this->toLinkedList()->reduce($callback);
    }

    /**
     * @return Generator<array{TK, TV}>
     */
    public function generatePairs(): Generator
    {
        foreach ($this as $pair) {
            yield $pair;
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
     * @return Generator<int, TV>
     */
    public function generateValues(): Generator
    {
        $i = 0;

        foreach ($this as $pair) {
            yield $i++ => $pair[1];
        }
    }
}
