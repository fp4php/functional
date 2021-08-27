<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractNonEmptySeq<TV>
 */
final class NonEmptyArrayList extends AbstractNonEmptySeq
{
    /**
     * @param ArrayList<TV> $arrayList
     */
    private function __construct(public ArrayList $arrayList)
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
        $isEmpty = true;
        $buffer = [];

        foreach ($source as $elem) {
            $isEmpty = false;
            $buffer[] = $elem;
        }

        if ($isEmpty) {
            throw new EmptyCollectionException("Non empty collection must contain at least one element");
        }

        return new self(new ArrayList($buffer));
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
        return $this->arrayList->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->arrayList->count();
    }

    /**
     * O(1) time/space complexity
     *
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return ($this->arrayList)($index);
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
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->head(), LinkedList::collect($this->tail()));
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptySeq
    {
        return new self($this->arrayList->appended($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        $source = function() use ($suffix): Generator {
            foreach ($this as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collectUnsafe($source());
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptySeq
    {
        return new self($this->arrayList->prepended($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        $source = function() use ($prefix): Generator {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collectUnsafe($source());
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return $this->arrayList->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->arrayList->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->arrayList->everyOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->arrayList->exists($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->arrayList->existsOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return ArrayList<TV>
     */
    public function filter(callable $predicate): ArrayList
    {
        return $this->arrayList->filter($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return ArrayList<TV>
     */
    public function filterNotNull(): ArrayList
    {
        return $this->arrayList->filterNotNull();
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return ArrayList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): ArrayList
    {
        return $this->arrayList->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return $this->arrayList->first($predicate);
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
        return $this->arrayList->firstOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return ArrayList<TVO>
     */
    public function flatMap(callable $callback): ArrayList
    {
        return $this->arrayList->flatMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return $this->arrayList->head()->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return $this->arrayList->last($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return new self($this->arrayList->map($callback));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param callable(TV|TVI, TV): (TV|TVI) $callback
     * @psalm-return (TV|TVI)
     */
    public function reduce(callable $callback): mixed
    {
        return $this->arrayList->reduce($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return new self($this->arrayList->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-return ArrayList<TV>
     */
    public function tail(): ArrayList
    {
        $isFirst = true;
        $buffer = [];

        foreach ($this as $elem) {
            if (!$isFirst) {
                $buffer[] = $elem;
            }

            $isFirst = false;
        }

        return new ArrayList($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        return new self($this->arrayList->unique($callback));
    }
}
