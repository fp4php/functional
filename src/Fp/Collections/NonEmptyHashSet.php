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
     * @param array<TVI>|Collection<TVI>|NonEmptyCollection<TVI>|PureIterable<TVI> $source
     * @return self<TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(array|Collection|NonEmptyCollection|PureIterable $source): self
    {
        return new self(HashSet::collect(PureIterable::of(function () use ($source) {
            $isEmpty = true;

            foreach ($source as $elem) {
                yield $elem;
                $isEmpty = false;
            }

            if ($isEmpty) {
                throw new EmptyCollectionException("Non empty collection must contain at least one element");
            }
        })));
    }

    /**
     * @psalm-pure
     * @template TVI
     * @param array<TVI>|Collection<TVI>|NonEmptyCollection<TVI>|PureIterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectUnsafe(array|Collection|NonEmptyCollection|PureIterable $source): self
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
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI>|PureIterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection|PureIterable $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @psalm-pure
     * @template TVI
     * @param array<TVI>|Collection<TVI>|NonEmptyCollection<TVI>|PureIterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collectOption(array|Collection|NonEmptyCollection|PureIterable $source): Option
    {
        try {
            return Option::some(self::collect($source));
        } catch (EmptyCollectionException) {
            return Option::none();
        }
    }

    /**
     * @inheritDoc
     * @return Iterator<TV>
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
        return self::collectUnsafe(PureIterable::of(function () use ($callback) {
            foreach ($this as $element) {
                yield $callback($element);
            }
        }));
    }
}
