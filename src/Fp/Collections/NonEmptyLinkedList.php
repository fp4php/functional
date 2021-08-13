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
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyLinkedList<TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): NonEmptyLinkedList
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
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyLinkedList<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyLinkedList
    {
        try {
            return self::collect($source);
        } catch (EmptyCollectionException $e) {
            throw new Error(previous: $e);
        }
    }

    /**
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param non-empty-array<TKI, TVI>|NonEmptyCollection<TKI, TVI> $source
     * @return NonEmptyLinkedList<TVI>
     */
    public static function collectNonEmpty(iterable $source): NonEmptyLinkedList
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
    function append(mixed $elem): NonEmptySeq
    {
        return self::collectUnsafe($this->toLinkedList()->append($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    function prepend(mixed $elem): NonEmptySeq
    {
        return new self($elem, $this->toLinkedList());
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    function anyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->toLinkedList()->anyOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    function at(int $index): Option
    {
        return $this->toLinkedList()->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    function every(callable $predicate): bool
    {
        return $this->toLinkedList()->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->toLinkedList()->everyOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    function exists(callable $predicate): bool
    {
        return $this->toLinkedList()->exists($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    function filter(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->filter($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    function filterNotNull(): LinkedList
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
    function filterOf(string $fqcn, bool $invariant = false): LinkedList
    {
        return $this->toLinkedList()->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    function first(callable $predicate): Option
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
    function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->toLinkedList()->firstOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return LinkedList<TVO>
     */
    function flatMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->flatMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV) $callback
     */
    function forAll(callable $callback): void
    {
        /** @psalm-suppress UnusedMethodCall */
        $this->toLinkedList()->forAll($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    function head(): mixed
    {
        return $this->head;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    function last(callable $predicate): Option
    {
        return $this->toLinkedList()->last($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptyLinkedList<TVO>
     */
    public function map(callable $callback): NonEmptyLinkedList
    {
        return self::collectUnsafe($this->toLinkedList()->map($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return TV
     */
    function reduce(callable $callback): mixed
    {
        return $this->toLinkedList()->reduce($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptyLinkedList<TV>
     */
    function reverse(): NonEmptyLinkedList
    {
        return self::collectUnsafe($this->toLinkedList()->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    function tail(): LinkedList
    {
        return $this->tail;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return NonEmptyLinkedList<TV>
     */
    function unique(callable $callback): NonEmptyLinkedList
    {
        return self::collectUnsafe($this->toLinkedList()->unique($callback));
    }
}
