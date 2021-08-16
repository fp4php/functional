<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Fp\Functional\Option\Option;
use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptyLinearSeq<TV>
 */
class NonEmptyLinkedList implements NonEmptyLinearSeq
{
    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(public mixed $head, public LinkedList $tail)
    {
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpureMethodCall
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): self
    {
        $collected = LinkedList::collect($source);

        if ($collected instanceof Cons) {
            /** @var Cons<TVI> $collected */
            $head = $collected->head;
            $tail = $collected->tail;
        } else {
            throw new EmptyCollectionException("Non empty collection must contain at least one element");
        }

        return new NonEmptyLinkedList($head, $tail);
    }

    /**
     * @psalm-pure
     * @template TVI
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
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
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
        return new LinkedListIterator($this->toLinkedList());
    }

    /**
     * @inheritDoc
     * @return non-empty-list<TV>
     */
    public function toArray(): array
    {
        $buffer = [$this->head];

        foreach ($this->tail as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return new Cons($this->head, $this->tail);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptySeq
    {
        return self::collectUnsafe($this->toLinkedList()->appended($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptySeq
    {
        return new self($elem, $this->toLinkedList());
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return $this->toLinkedList()->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->toLinkedList()->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->toLinkedList()->everyOf($fqcn, $invariant);
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
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->toLinkedList()->existsOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->filter($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return $this->toLinkedList()->filterNotNull();
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return LinkedList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): LinkedList
    {
        return $this->toLinkedList()->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return $this->toLinkedList()->first($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->toLinkedList()->firstOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return LinkedList<TVO>
     */
    public function flatMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->flatMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return $this->head;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return $this->toLinkedList()->last($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        $last = $this->head;

        foreach ($this->tail as $element) {
            $last = $element;
        }

        return $last;
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collectUnsafe($this->toLinkedList()->map($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return TV
     */
    public function reduce(callable $callback): mixed
    {
        return $this->toLinkedList()->reduce($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return self::collectUnsafe($this->toLinkedList()->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return $this->tail;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        return self::collectUnsafe($this->toLinkedList()->unique($callback));
    }
}
