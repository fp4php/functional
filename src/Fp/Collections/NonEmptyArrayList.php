<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
final class NonEmptyArrayList implements NonEmptySeq
{
    /**
     * @use NonEmptySeqChainable<TV>
     */
    use NonEmptySeqChainable;

    /**
     * @use NonEmptySeqUnchainable<TV>
     */
    use NonEmptySeqUnchainable;

    /**
     * @use NonEmptySeqConvertible<TV>
     */
    use NonEmptySeqConvertible;

    /**
     * @internal
     * @param ArrayList<TV> $arrayList
     */
    public function __construct(public ArrayList $arrayList)
    {
    }

    /**
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $buffer = [];

        foreach ($source as $elem) {
            $buffer[] = $elem;
        }

        return Option::condLazy(isset($buffer[0]), fn() => new self(new ArrayList($buffer)));
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
     * @inheritDoc
     * @return non-empty-list<TV>
     */
    public function toArray(): array
    {
        /** @var non-empty-list<TV> */
        return $this->arrayList->elements;
    }

    /**
     * @inheritDoc
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return $this->arrayList;
    }

    /**
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return $this;
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
     * @psalm-return ArrayList<TV>
     */
    public function filter(callable $predicate): ArrayList
    {
        return $this->arrayList->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return ArrayList<TVO>
     */
    public function filterMap(callable $callback): ArrayList
    {
        return $this->arrayList->filterMap($callback);
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
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return new self($this->arrayList->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return ArrayList<TV>
     */
    public function takeWhile(callable $predicate): ArrayList
    {
        return $this->arrayList->takeWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return ArrayList<TV>
     */
    public function dropWhile(callable $predicate): ArrayList
    {
        return $this->arrayList->dropWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return ArrayList<TV>
     */
    public function take(int $length): ArrayList
    {
        return $this->arrayList->take($length);
    }

    /**
     * @inheritDoc
     * @psalm-return ArrayList<TV>
     */
    public function drop(int $length): ArrayList
    {
        return $this->arrayList->drop($length);
    }
}
