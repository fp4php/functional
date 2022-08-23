<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Functional\WithExtensions;
use Fp\Operations as Ops;
use Fp\Operations\FoldOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\fromPairs;
use function Fp\Collection\keys;

/**
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 *
 * @psalm-seal-methods
 * @mixin NonEmptyLinkedListExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class NonEmptyLinkedList implements NonEmptySeq
{
    use WithExtensions;

    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(public mixed $head, public LinkedList $tail)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return NonEmptyLinkedList<TVI>
     */
    public static function singleton(mixed $val): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectNonEmpty([$val]);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return Option<NonEmptyLinkedList<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return Option::some(LinkedList::collect($source))
            ->filter(fn($list) => $list instanceof Cons)
            ->map(fn(Cons $cons) => new NonEmptyLinkedList($cons->head, $cons->tail));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return NonEmptyLinkedList<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collect($source)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<TVI> $source
     * @return NonEmptyLinkedList<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($source);
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->toLinkedList());
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->tail->count() + 1;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->filter($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return LinkedList<TVO>
     */
    public function filterMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->filterMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return $this->toLinkedList()->filterNotNull();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return LinkedList<TVO>
     */
    public function filterOf(string|array $fqcn, bool $invariant = false): LinkedList
    {
        return $this->toLinkedList()->filterOf($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<TVO>) $callback
     * @return LinkedList<TVO>
     */
    public function flatMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->flatMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function reverse(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this->toLinkedList()->reverse());
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return $this->tail;
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function init(): ArrayList
    {
        return ArrayList::collect(Ops\InitOperation::of($this->getIterator())());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->takeWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->dropWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function take(int $length): LinkedList
    {
        return $this->toLinkedList()->take($length);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function drop(int $length): LinkedList
    {
        return $this->toLinkedList()->drop($length);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyLinkedList<TVO>
     */
    public function map(callable $callback): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\MapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return NonEmptyLinkedList<TVO>
     */
    public function mapN(callable $callback): NonEmptyLinkedList
    {
        return $this->map(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return $callback(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $suffix
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $prefix
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function tap(callable $callback): NonEmptyLinkedList
    {
        Stream::emits(Ops\TapOperation::of($this->getIterator())(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<TVI> | NonEmptyCollection<TVI> $that
     * @return NonEmptyLinkedList<array{TV, TVI}>
     */
    public function zip(iterable $that): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\ZipOperation::of($this->getIterator())($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<array{int, TV}>
     */
    public function zipWithKeys(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\ZipOperation::of(keys($this->getIterator()))($this->getIterator()));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV, TV): int $cmp
     * @return NonEmptyLinkedList<TV>
     */
    public function sorted(callable $cmp): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function at(int $index): Option
    {
        return Ops\AtOperation::of($this->getIterator())($index);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-assert-if-true NonEmptySeq<TVO> $this
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function everyOf(string|array $fqcn, bool $invariant = false): bool
    {
        return Ops\EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptyLinkedList<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => NonEmptyLinkedList::collectUnsafe($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<Option<TVO>>
     *
     * @return Option<NonEmptyLinkedList<TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this->getIterator())
            ->map(fn($gen) => NonEmptyLinkedList::collectUnsafe($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return NonEmptyHashMap<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): NonEmptyHashMap
    {
        return new NonEmptyHashMap(Ops\GroupMapReduceOperation::of($this->getIterator())(dropFirstArg($group), dropFirstArg($map), $reduce));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyHashMap<TKO, TV>
     */
    public function reindex(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap(
            HashMap::collect(Ops\ReindexOperation::of($this->getIterator())(dropFirstArg($callback))),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return Ops\ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return Ops\FirstOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Option<TVO>
     */
    public function firstOf(string|array $fqcn, bool $invariant = false): Option
    {
        return Ops\FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Option<TVO>
     */
    public function lastOf(string|array $fqcn, bool $invariant = false): Option
    {
        return Ops\LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function head(): mixed
    {
        return $this->head;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return Ops\LastOperation::of($this->getIterator())(dropFirstArg($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldOperation<TV, TVO>
     */
    public function fold(mixed $init): FoldOperation
    {
        return new FoldOperation($this->getIterator(), $init);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function lastElement(): mixed
    {
        return Ops\LastOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptyLinkedList<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap
    {
        $groups = Ops\GroupByOperation::of($this)(dropFirstArg($callback));

        return (new NonEmptyHashMap($groups))
            ->map(fn(NonEmptyHashMap $elem) => $elem->values()->toNonEmptyLinkedList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return NonEmptyMap<TKO, NonEmptyArrayList<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap
    {
        $groups = Ops\GroupMapOperation::of($this)(dropFirstArg($group), dropFirstArg($map));

        return (new NonEmptyHashMap($groups))
            ->map(fn(NonEmptyHashMap $elem) => $elem->values()->toNonEmptyArrayList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return NonEmptyLinkedList<TV | TVI>
     */
    public function intersperse(mixed $separator): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe(Ops\IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        $buffer = [$this->head()];

        foreach ($this->tail() as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * {@inheritDoc}
     *
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array
    {
        /** @var non-empty-list<TV> */
        return $this->toList();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return fromPairs($this);
    }

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toNonEmptyArray();
     * => ['fst' => 1, 'snd' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<array{TKO, TVO}>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyArray(): array
    {
        /** @var non-empty-array<TKO, TVO> */
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return new Cons($this->head, $this->tail);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyLinkedList<array{TKI, TVI}>
     *
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(): HashMap
    {
        return HashMap::collectPairs($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyLinkedList<array{TKI, TVI}>
     *
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsNonEmpty($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return Stream<TV>
     */
    public function toStream(): Stream
    {
        return Stream::emits($this);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->toLinkedList()
            ->mkString('NonEmptyLinkedList(', ', ', ')');
    }
}
