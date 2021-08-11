<?php

declare(strict_types=1);

namespace Fp\Collections;

use Generator;
use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
class NonEmptyLinkedList implements NonEmptySeq
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
     *
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyLinkedList<TVI>
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
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator(new Cons($this->head, $this->tail));
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptyLinkedList<TVO>
     */
    public function map(callable $callback): NonEmptyLinkedList
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                yield $callback($element);
            }
        };

        return self::collect($source());
    }

    /**
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return $this->head;
    }

    /**
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        $source = function () use ($predicate): Generator {
            foreach ($this as $element) {
                if ($predicate($element)) {
                    yield $element;
                }
            }
        };

        return LinkedList::collect($source());
    }
}
