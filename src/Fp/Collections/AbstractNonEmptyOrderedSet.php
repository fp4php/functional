<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptyOrderedSet<TV>
 */
abstract class AbstractNonEmptyOrderedSet implements NonEmptyOrderedSet
{
    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    abstract public static function collect(iterable $source): Option;

    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    abstract public static function collectUnsafe(iterable $source): self;

    /**
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
     * @return self<TVI>
     */
    abstract public static function collectNonEmpty(array|NonEmptyCollection $source): self;

    /**
     * @inheritDoc
     * @return Iterator<TV>
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
     * @return non-empty-list<TV>
     */
    public function toArray(): array
    {
        $buffer = [$this->head()];

        foreach ($this->tail() as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this);
    }

    /**
     * @inheritDoc
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this);
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    abstract public function toHashSet(): HashSet;

    /**
     * @inheritDoc
     * @return NonEmptyHashSet<TV>
     */
    abstract public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap
    {
        return HashMap::collectPairs(IterableOnce::of(function () use ($callback) {
            foreach ($this as $elem) {
                yield $callback($elem);
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(callable $callback): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsUnsafe(IterableOnce::of(function () use ($callback) {
            foreach ($this as $elem) {
                yield $callback($elem);
            }
        }));
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
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this as $element) {
            if (!$predicate($element)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->every(fn(mixed $v) => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->first($predicate)->isSome();
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->firstOf($fqcn, $invariant)->fold(
            fn() => true,
            fn() => false,
        );
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        $first = null;

        foreach ($this as $element) {
            if ($predicate($element)) {
                $first = $element;
                break;
            }
        }

        return Option::fromNullable($first);
    }


    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        $last = null;

        foreach ($this as $element) {
            if ($predicate($element)) {
                $last = $element;
            }
        }

        return Option::fromNullable($last);
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
        /** @var Option<TVO> */
        return $this->first(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed
    {
        $acc = $this->head();

        foreach ($this->tail() as $element) {
            $acc = $callback($acc, $element);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function head(): mixed
    {
        $head = null;

        foreach ($this as $element) {
            $head = $element;
            break;
        }

        return Option::fromNullable($head)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        return $this->last(fn() => true)->getUnsafe();
    }

    /**
     * @inheritDoc
     */
    public function subsetOf(Set|NonEmptySet $superset): bool
    {
        $isSubset = true;

        foreach ($this as $elem) {
            if (!$superset($elem)) {
                $isSubset = false;
                break;
            }
        }

        return $isSubset;
    }
}
