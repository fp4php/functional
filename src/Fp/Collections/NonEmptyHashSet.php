<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Error;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;

/**
 * @template-covariant TV of (object|scalar)
 * @psalm-immutable
 * @implements NonEmptySet<TV>
 */
class NonEmptyHashSet implements NonEmptySet
{
    /**
     * @param HashSet<TV> $set
     */
    private function __construct(private HashSet $set)
    {
    }

    /**
     * @inheritDoc
     * @return non-empty-list<TV>
     */
    public function toArray(): array
    {
        $list = $this->set->toLinkedList();

        return [$list->head()->getUnsafe(), ...$list->tail()->toArray()];
    }

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->toNonEmptyLinkedList()->toLinkedList();
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        $list = $this->set->toLinkedList();

        return new NonEmptyLinkedList($list->head()->getUnsafe(), $list->tail());
    }

    /**
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param iterable<TVI> $source
     * @return self<TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): self
    {
        $collected = LinkedList::collect($source);

        if ($collected instanceof Nil) {
            throw new EmptyCollectionException("Non empty collection must contain at least one element");
        }

        /** @var self<TVI> */
        return new self(HashSet::collect($collected));
    }

    /**
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param iterable<TVI> $source
     * @return self<TVI>
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
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param non-empty-array<TVI>|NonEmptySet<TVI>|NonEmptySeq<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(iterable $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->toArray());
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
        return $this->set->contains($element);
    }

    /**
     * @inheritDoc
     * @template TVI of (object|scalar)
     * @param TVI $element
     * @return self<TV|TVI>
     */
    public function updated(mixed $element): self
    {
        return new self($this->set->updated($element));
    }

    /**
     * @inheritDoc
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set
    {
        return $this->set->removed($element);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->set->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->set->exists($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return TV
     */
    public function reduce(callable $callback): mixed
    {
        return $this->set->reduce($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TVO of (object|scalar)
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                yield $callback($element);
            }
        };

        return self::collectUnsafe($source());
    }
}
