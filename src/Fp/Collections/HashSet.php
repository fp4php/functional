<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;
use Iterator;

/**
 * @template-covariant TV of (object|scalar)
 * @psalm-immutable
 * @implements Set<TV>
 */
class HashSet implements Set
{
    /**
     * @param Map<TV, TV> $map
     */
    private function __construct(private Map $map)
    {
    }

    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        return $this->toLinkedList()->toArray();
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->map->toLinkedList()->map(fn($pair) => $pair[1]);
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $pairs = LinkedList::collect($source)->map(fn(mixed $elem) => [$elem, $elem]);

        /**
         * Inference isn't working in generic context
         * @var self<TVI>
         */
        return new self(HashMap::collect($pairs));
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->toLinkedList());
    }

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->map->get($element)->isNonEmpty();
    }

    /**
     * @inheritDoc
     * @template TVI of (object|scalar)
     * @param TVI $element
     * @return Set<TV|TVI>
     */
    public function updated(mixed $element): Set
    {
        return new self($this->map->updated($element, $element));
    }

    /**
     * @inheritDoc
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set
    {
        return new self($this->map->removed($element));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->map->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->toLinkedList()->exists($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set
    {
        return new self($this->map->filter($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO of (object|scalar)
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function flatMap(callable $callback): Set
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                $result = $callback($element);

                foreach ($result as $item) {
                    yield $item;
                }
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-param TV $init initial accumulator value
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current element): new accumulator
     * @psalm-return TV
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return $this->toLinkedList()->fold($init, $callback);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV>
     */
    public function reduce(callable $callback): Option
    {
        return $this->toLinkedList()->reduce($callback);
    }

    /**
     * @inheritDoc
     * @template TVO of (object|scalar)
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return Set<TVO>
     */
    public function map(callable $callback): Set
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                yield $callback($element);
            }
        };

        return self::collect($source());
    }
}
