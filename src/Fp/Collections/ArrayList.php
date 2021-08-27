<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;

use function Fp\of;

/**
 * O(1) {@see Seq::at()} and {@see IndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractSeq<TV>
 */
final class ArrayList extends AbstractSeq
{
    /**
     * @param list<TV> $elements
     */
    public function __construct(public array $elements)
    {
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $buffer = [];

        foreach ($source as $elem) {
            $buffer[] = $elem;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * O(1) time/space complexity
     *
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this->elements as $elem) {
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
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return new self([...$this->elements, $elem]);
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
            foreach ($this->elements as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return new self([$elem, ...$this->elements]);
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

            foreach ($this->elements as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collect($source());
    }

    /**
     * O(1) time/space complexity
     *
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return Option::fromNullable($this->elements[$index] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        $result = true;

        foreach ($this->elements as $element) {
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
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                $buffer[] = $element;
            }
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return $this->filter(fn(mixed $v) => !is_null($v));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return self<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        /** @var self<TVO> */
        return $this->filter(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        $first = null;

        foreach ($this->elements as $element) {
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
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            $result = $callback($element);

            foreach ($result as $item) {
                $buffer[] =  $item;
            }
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $init initial accumulator value
     * @psalm-param callable(TVI, TV): TVI $callback (accumulator, current element): new accumulator
     * @psalm-return TVI
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        $acc = $init;

        foreach ($this->elements as $element) {
            $acc = $callback($acc, $element);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param callable(TV|TVI, TV): (TV|TVI) $callback
     * @psalm-return Option<TV|TVI>
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
        return Option::fromNullable($this->elements[0] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        $last = null;

        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                $last = $element;
            }
        }

        return Option::fromNullable($last);
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            $buffer[] = $callback($element);
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->elements));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        $buffer = $this->toArray();
        array_shift($buffer);
        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        $pairs = $this->map(function($elem) use ($callback) {
            /** @var TV $elem */
            return [$callback($elem), $elem];
        });

        return self::collect(HashMap::collect($pairs)->values());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function takeWhile(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if (!$predicate($element)) {
                break;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function dropWhile(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                continue;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function take(int $length): self
    {
        $buffer = [];

        foreach ($this->elements as $i => $element) {
            if ($i === $length) {
                break;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function drop(int $length): self
    {
        $buffer = [];

        foreach ($this->elements as $i => $element) {
            if ($i < $length) {
                continue;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return Map<TKO, Seq<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        $buffer = new HashMapBuffer();

        foreach ($this as $elem) {
            $key = $callback($elem);

            /** @var Seq<TV> $group */
            $group = $buffer->get($key)->getOrElse(Nil::getInstance());

            $buffer->update($key, $group->prepended($elem));
        }

        return $buffer->toHashMap();
    }
}
