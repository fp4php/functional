<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
abstract class AbstractNonEmptySeq implements NonEmptySeq
{
    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     * @throws EmptyCollectionException
     */
    abstract public static function collect(iterable $source): self;

    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    abstract public static function collectUnsafe(iterable $source): self;

    /**
     * @psalm-pure
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
     * @return self<TVI>
     */
    abstract public static function collectNonEmpty(iterable $source): self;

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    abstract public function getIterator(): Iterator;

    /**
     * Alias for {@see NonEmptySeq::at()}
     *
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        $first = null;

        foreach ($this as $idx => $element) {
            if ($idx === $index) {
                $first = $element;
                break;
            }
        }

        return Option::fromNullable($first);
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
     * @template TVI
     * @psalm-param callable(TV|TVI, TV): (TV|TVI) $callback
     * @psalm-return (TV|TVI)
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
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return Map<TKO, NonEmptySeq<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        $buffer = new HashMapBuffer();

        foreach ($this as $elem) {
            $key = $callback($elem);

            /** @psalm-var Option<NonEmptySeq<TV>> $optionalGroup */
            $optionalGroup = $buffer->get($key);

            $buffer->update($key, $optionalGroup->fold(
                fn(NonEmptySeq $group): NonEmptySeq => $group->prepended($elem),
                fn(): NonEmptySeq => new NonEmptyLinkedList($elem, Nil::getInstance())
            ));
        }

        return $buffer->toHashMap();
    }
}
