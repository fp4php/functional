<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;
use Iterator;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements Set<TV>
 */
abstract class AbstractSet implements Set
{
    /**
     * REPL:
     * >>> HashSet::collect([1, 2])
     * => HashSet(1, 2)
     *
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    abstract public static function collect(iterable $source): self;

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
     * @return list<TV>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this as $elem) {
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
     * @return HashSet<TV>
     */
    abstract public function toHashSet(): HashSet;

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap
    {
        return HashMap::collectPairs(PureIterable::of(function () use ($callback) {
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
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        $acc = $init;

        foreach ($this as $element) {
            $acc = $callback($acc, $element);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        return $this->head()->map(function ($head) use ($callback) {
            /** @var TV $acc */
            $acc = $head;

            foreach ($this->tail() as $element) {
                $acc = $callback($acc, $element);
            }

            return $acc;
        });
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        $head = null;

        foreach ($this as $element) {
            $head = $element;
            break;
        }

        return Option::fromNullable($head);
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
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option
    {
        return $this->last(fn() => true);
    }
}
