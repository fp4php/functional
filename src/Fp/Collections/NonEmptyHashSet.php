<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

use function Fp\Callable\asGenerator;
use function Fp\of;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @implements NonEmptySet<TV>
 */
final class NonEmptyHashSet implements NonEmptySet
{
    /**
     * @internal
     * @param HashSet<TV> $set
     */
    public function __construct(private HashSet $set)
    {
    }

    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $hashset = HashSet::collect($source);
        return Option::condLazy(!$hashset->isEmpty(), fn() => new self($hashset));
    }

    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectUnsafe(iterable $source): self
    {
        return self::collect($source)->getUnsafe();
    }

    /**
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): self
    {
        return self::collectUnsafe($source);
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
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap
    {
        return HashMap::collectPairs(asGenerator(function () use ($callback) {
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
        return NonEmptyHashMap::collectPairsUnsafe(asGenerator(function () use ($callback) {
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
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Set<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Set
    {
        /** @var Set<TVO> */
        return $this->filter(fn(mixed $v): bool => of($v, $fqcn, $invariant));
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
        return self::collectUnsafe(asGenerator(function () use ($callback) {
            foreach ($this as $element) {
                yield $callback($element);
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function flatMap(callable $callback): Set
    {
        return HashSet::collect(asGenerator(function () use ($callback) {
            foreach ($this as $element) {
                foreach ($callback($element) as $item) {
                    yield $item;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
     */
    public function tap(callable $callback): self
    {
        foreach ($this as $elem) {
            $callback($elem);
        }

        return $this;
    }
}
