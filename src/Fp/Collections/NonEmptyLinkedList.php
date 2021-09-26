<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\AppendedAllOperation;
use Fp\Operations\AppendedOperation;
use Fp\Operations\MapValuesOperation;
use Fp\Operations\PrependedAllOperation;
use Fp\Operations\PrependedOperation;
use Fp\Operations\SortedOperation;
use Fp\Operations\TapOperation;
use Fp\Operations\UniqueOperation;
use Fp\Streams\Stream;
use Generator;
use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
final class NonEmptyLinkedList implements NonEmptySeq
{
    /**
     * @use NonEmptySeqTerminable<TV>
     */
    use NonEmptySeqTerminable;

    /**
     * @use NonEmptySeqCastable<TV>
     */
    use NonEmptySeqCastable;

    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(public mixed $head, public LinkedList $tail)
    {
    }

    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return Option::some(LinkedList::collect($source))
            ->filter(fn($list) => $list instanceof Cons)
            ->map(fn(Cons $cons) => new NonEmptyLinkedList($cons->head, $cons->tail));
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
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->toLinkedList());
    }

    /**
     * @return Generator<int, TV>
     */
    private function iter(): Generator
    {
        foreach ($this as $elem) {
            yield $elem;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->tail->count() + 1;
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return new Cons($this->head, $this->tail);
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return LinkedList<TVO>
     */
    public function filterMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->filterMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return $this->toLinkedList()->filterNotNull();
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return LinkedList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): LinkedList
    {
        return $this->toLinkedList()->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return LinkedList<TVO>
     */
    public function flatMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->flatMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return $this->head;
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return self::collectUnsafe($this->toLinkedList()->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return $this->tail;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->takeWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->dropWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function take(int $length): LinkedList
    {
        return $this->toLinkedList()->take($length);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function drop(int $length): LinkedList
    {
        return $this->toLinkedList()->drop($length);
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collectUnsafe(MapValuesOperation::of($this->iter())($callback));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return self::collectUnsafe(AppendedOperation::of($this->iter())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collectUnsafe(AppendedAllOperation::of($this->iter())($suffix));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return self::collectUnsafe(PrependedOperation::of($this->iter())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collectUnsafe(PrependedAllOperation::of($this->iter())($prefix));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
     */
    public function tap(callable $callback): self
    {
        Stream::emits(TapOperation::of($this->iter())($callback))->drain();
        return $this;
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-param callable(TV): (int|string) $callback
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        return self::collectUnsafe(UniqueOperation::of($this->iter())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return self<TV>
     */
    public function sorted(callable $cmp): self
    {
        return self::collectUnsafe(SortedOperation::of($this->iter())($cmp));
    }
}
