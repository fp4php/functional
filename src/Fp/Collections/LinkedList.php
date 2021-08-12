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
 * @implements LinearSeq<TV>
 */
class LinkedList implements LinearSeq
{
    /**
     * @inheritDoc
     * @psalm-pure
     * @psalm-suppress ImpureMethodCall
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return LinkedList<TVI>
     */
    public static function collect(iterable $source): LinkedList
    {
        $list = new Nil();

        foreach ($source as $element) {
            $list = new Cons($element, $list);
        }

        return $list;
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    function anyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->firstOf($fqcn, $invariant)->fold(
            fn() => true,
            fn() => false,
        );
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    function at(int $index): Option
    {
        return $this->first(fn(mixed $v, mixed $k) => $k === $index);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    function every(callable $predicate): bool
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
    function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->every(fn(mixed $v) => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    function exists(callable $predicate): bool
    {
        return $this->first($predicate)->isSome();
    }

    /**
     * @inheritDoc
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

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    function filterNotNull(): LinkedList
    {
        return $this->filter(fn(mixed $v) => !is_null($v));
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
        /** @var LinkedList<TVO> */
        return $this->filter(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, int): bool $predicate
     * @psalm-return Option<TV>
     */
    function first(callable $predicate): Option
    {
        $first = null;

        foreach ($this as $index => $element) {
            if ($predicate($element, $index)) {
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
    function firstOf(string $fqcn, bool $invariant = false): Option
    {
        /** @var Option<TVO> */
        return $this->first(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return LinkedList<TVO>
     */
    function flatMap(callable $callback): LinkedList
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                $result = $callback($element);

                foreach ($result as $item) {
                    yield $item;
                }
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-param TV $init initial accumulator value
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current element): new accumulator
     * @psalm-return TV
     */
    function fold(mixed $init, callable $callback): mixed
    {
        $acc = $init;

        foreach ($this as $element) {
            $acc = $callback($acc, $element);
        }

        return $acc;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV) $callback
     */
    function forAll(callable $callback): void
    {
        foreach ($this as $element) {
            $callback($element);
        }
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
     * @psalm-param null|callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    function last(?callable $predicate = null): Option
    {
        $last = null;

        foreach ($this as $element) {
            if (is_null($predicate) || $predicate($element)) {
                $last = $element;
            }
        }

        return Option::fromNullable($last);
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return LinkedList<TVO>
     */
    public function map(callable $callback): LinkedList
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                yield $callback($element);
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV>
     */
    function reduce(callable $callback): Option
    {
        return $this->head()->map(function ($head) use ($callback) {
            /**
             * @var TV $head TODO
             */
            return $this->tail()->fold($head, $callback);
        });
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    function reverse(): LinkedList
    {
        return self::collect($this);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    function tail(): LinkedList
    {
        return match (true) {
            $this instanceof Cons => $this->tail,
            $this instanceof Nil => $this,
        };
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return LinkedList<TV>
     */
    function unique(callable $callback): LinkedList
    {
        $source = function () use ($callback): Generator {
            $hashTable = [];

            foreach ($this as $element) {
                $elementHash = $callback($element);
                $isPresent = isset($hashTable[$elementHash]);

                if (!$isPresent) {
                    yield $element;
                    $hashTable[$elementHash] = true;
                }
            }
        };

        return self::collect($source());
    }
}
