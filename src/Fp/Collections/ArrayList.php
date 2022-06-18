<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Fp\Functional\Option\Option;
use Fp\Operations\AppendedAllOperation;
use Fp\Operations\AppendedOperation;
use Fp\Operations\DropOperation;
use Fp\Operations\DropWhileOperation;
use Fp\Operations\MapOperation;
use Fp\Operations\TraverseOptionOperation;
use Fp\Operations\EveryOfOperation;
use Fp\Operations\EveryOperation;
use Fp\Operations\ExistsOfOperation;
use Fp\Operations\ExistsOperation;
use Fp\Operations\FilterMapOperation;
use Fp\Operations\FilterNotNullOperation;
use Fp\Operations\FilterOfOperation;
use Fp\Operations\FilterOperation;
use Fp\Operations\FirstOfOperation;
use Fp\Operations\FirstOperation;
use Fp\Operations\FlatMapOperation;
use Fp\Operations\FoldOperation;
use Fp\Operations\GroupByOperation;
use Fp\Operations\IntersperseOperation;
use Fp\Operations\LastOfOperation;
use Fp\Operations\LastOperation;
use Fp\Operations\MkStringOperation;
use Fp\Operations\PrependedAllOperation;
use Fp\Operations\PrependedOperation;
use Fp\Operations\ReduceOperation;
use Fp\Operations\SortedOperation;
use Fp\Operations\TailOperation;
use Fp\Operations\TakeOperation;
use Fp\Operations\TakeWhileOperation;
use Fp\Operations\TapOperation;
use Fp\Operations\UniqueOperation;
use Fp\Operations\ZipOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Cast\asGenerator;

/**
 * O(1) {@see Seq::at()} and {@see Seq::__invoke} operations
 *
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @implements Seq<TV>
 */
final class ArrayList implements Seq
{
    /**
     * @param list<TV> $elements
     */
    public function __construct(public array $elements) { }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
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
     * @template TVI
     * @param TVI $val
     * @return self<TVI>
     */
    public static function singleton(mixed $val): self
    {
        return new self([$val]);
    }

    /**
     * @inheritDoc
     * @return self<empty>
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @inheritDoc
     * @psalm-param positive-int $by
     * @psalm-return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self
    {
        return Stream::range($start, $stopExclusive, $by)->toArrayList();
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        return $this->elements;
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
        return $this;
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option
    {
        $that = $this;
        return Option::when(
            $this->isNonEmpty(),
            fn() => new NonEmptyArrayList($that)
        );
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
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
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
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        return Option::fromNullable($this->elements[0] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        return self::collect(TailOperation::of($this->getIterator())());
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
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return Option<self<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return TraverseOptionOperation::of($this->getIterator())($callback)
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return ExistsOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return FirstOperation::of($this->getIterator())($predicate);
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
        return FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return LastOperation::of($this->getIterator())($predicate);
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
        // TODO known size optimization
        return LastOperation::of($this->getIterator())();
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
        return FoldOperation::of($this->getIterator())($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        return ReduceOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return Map<TKO, Seq<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        return GroupByOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true non-empty-list<TV> $this->elements
     */
    public function isNonEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @inheritDoc
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapOperation::withoutKey($this->getIterator(), $callback));
    }

    /**
     * @inheritDoc
     *
     * @template TVO
     *
     * @param callable(int, TV): TVO $callback
     * @return self<TVO>
     */
    public function mapWithKey(callable $callback): self
    {
        return self::collect(MapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return self::collect(AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collect(AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return self::collect(PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collect(PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return self::collect(FilterOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(FilterMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->getIterator())());
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
        return self::collect(FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collect(FlatMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function takeWhile(callable $predicate): self
    {
        return self::collect(TakeWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function dropWhile(callable $predicate): self
    {
        return self::collect(DropWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function take(int $length): self
    {
        return self::collect(TakeOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function drop(int $length): self
    {
        return self::collect(DropOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
     */
    public function tap(callable $callback): self
    {
        Stream::emits(TapOperation::of($this->getIterator())($callback))->drain();
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
        return self::collect(UniqueOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return self<TV>
     */
    public function sorted(callable $cmp): self
    {
        return self::collect(SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $separator
     * @psalm-return self<TV|TVI>
     */
    public function intersperse(mixed $separator): self
    {
        return self::collect(IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $that
     * @return self<array{TV, TVI}>
     */
    public function zip(iterable $that): self
    {
        return self::collect(ZipOperation::of($this->getIterator())($that));
    }

    /**
     * @inheritDoc
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }
}
