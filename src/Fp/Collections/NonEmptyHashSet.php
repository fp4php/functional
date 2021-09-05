<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Error;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @extends AbstractNonEmptySet<TV>
 */
final class NonEmptyHashSet extends AbstractNonEmptySet
{
    /**
     * @internal
     * @param HashSet<TV> $set
     */
    public function __construct(private HashSet $set)
    {
    }

    /**
     * @psalm-pure
     * @template TVI
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
     * @param non-empty-array<TVI>|NonEmptySet<TVI>|NonEmptySeq<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(iterable $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
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
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return $this->set->getIterator();
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return $this->set;
    }

    /**
     * @inheritDoc
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return $this;
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
     * @psalm-return HashSet<TV>
     */
    public function tail(): HashSet
    {
        return $this->set->tail();
    }

    /**
     * @inheritDoc
     * @template TVI
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
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return Set<TVO>
     */
    public function filterMap(callable $callback): Set
    {
        return $this->set->filterMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return Set<TV>
     */
    public function filterNotNull(): Set
    {
        return $this->filter(fn($elem) => null !== $elem);
    }

    /**
     * @inheritDoc
     * @template TVO
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
