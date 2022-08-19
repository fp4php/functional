<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Operations\FoldingOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Cast\asList;
use function Fp\Cast\fromPairs;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

/**
 * O(1) {@see Seq::prepended} operation
 * Fast {@see Seq::reverse} operation
 *
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @implements Seq<TV>
 */
abstract class LinkedList implements Seq
{
    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return LinkedList<TVI>
     */
    public static function collect(iterable $source): LinkedList
    {
        $buffer = new LinkedListBuffer();

        foreach ($source as $elem) {
            $buffer->append($elem);
        }

        return $buffer->toLinkedList();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return LinkedList<TVI>
     */
    public static function singleton(mixed $val): LinkedList
    {
        return new Cons($val, Nil::getInstance());
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<never>
     */
    public static function empty(): LinkedList
    {
        return Nil::getInstance();
    }

    /**
     * {@inheritDoc}
     *
     * @param positive-int $by
     * @return LinkedList<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): LinkedList
    {
        return Stream::range($start, $stopExclusive, $by)->toLinkedList();
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        return asList($this->getIterator());
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<non-empty-list<TV>>
     */
    public function toNonEmptyList(): Option
    {
        return proveNonEmptyList($this->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is LinkedList<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array
    {
        return fromPairs($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is LinkedList<array{TKO, TVO}>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     */
    public function toNonEmptyArray(): Option
    {
        return proveNonEmptyArray($this->toArray());
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<NonEmptyLinkedList<TV>>
     */
    public function toNonEmptyLinkedList(): Option
    {
        return NonEmptyLinkedList::collect($this);
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
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option
    {
        return NonEmptyArrayList::collect($this);
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
     * @return Option<NonEmptyHashSet<TV>>
     */
    public function toNonEmptyHashSet(): Option
    {
        return NonEmptyHashSet::collect($this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is LinkedList<array{TKI, TVI}>
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
     * @psalm-if-this-is LinkedList<array{TKI, TVI}>
     *
     * @return Option<NonEmptyHashMap<TKI, TVI>>
     */
    public function toNonEmptyHashMap(): Option
    {
        return NonEmptyHashMap::collectPairs($this);
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

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function reverse(): LinkedList
    {
        $list = Nil::getInstance();

        foreach ($this as $elem) {
            $list = $list->prepended($elem);
        }

        return $list;
    }

    /**
     * @psalm-assert-if-true Cons<TV> $this
     */
    public function isCons(): bool
    {
        return $this instanceof Cons;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return !$this->isCons();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this->getIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return Ops\EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<LinkedList<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getIterator())($callback)
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is LinkedList<Option<TVO>>
     *
     * @return Option<LinkedList<TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this->getIterator())
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return Ops\ExistsOperation::of($this->getIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
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
        return Ops\FirstOperation::of($this->getIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return Ops\FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return Ops\LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldingOperation<TV, TVO>
     */
    public function fold(mixed $init): FoldingOperation
    {
        return new FoldingOperation($this->getIterator(), $init);
    }

    /**
     * {@inheritDoc}
     *
     * @template TA
     *
     * @param callable(TV|TA, TV): (TV|TA) $callback
     * @return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        return Ops\ReduceOperation::of($this->getIterator())($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function head(): Option
    {
        return $this->isCons()
            ? Option::some($this)->map(fn(Cons $cons) => $cons->head)
            : Option::none();
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return match (true) {
            $this instanceof Cons => $this->tail,
            $this instanceof Nil => $this,
        };
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return Ops\LastOperation::of($this->getIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function lastElement(): Option
    {
        return Ops\LastOperation::of($this->getIterator())();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return Ops\CountOperation::of($this->getIterator())();
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
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, NonEmptyLinkedList<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        return Ops\GroupByOperation::of($this->getIterator())($callback)
            ->map(fn(NonEmptyHashMap $neSeq) => $neSeq->values()->toNonEmptyLinkedList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return HashMap<TKO, NonEmptyArrayList<TVO>>
     */
    public function groupMap(callable $group, callable $map): HashMap
    {
        return Ops\GroupMapOperation::of($this->getIterator())($group, $map)
            ->map(fn(NonEmptyHashMap $hs) => $hs->values()->toNonEmptyArrayList());
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
     * @return Map<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): Map
    {
        return Ops\GroupMapReduceOperation::of($this->getIterator())($group, $map, $reduce);
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindex(callable $callback): HashMap
    {
        return HashMap::collect(Ops\ReindexOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return LinkedList<TVO>
     */
    public function map(callable $callback): LinkedList
    {
        return LinkedList::collect(Ops\MapOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return LinkedList<TV|TVI>
     */
    public function appended(mixed $elem): LinkedList
    {
        return LinkedList::collect(Ops\AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $suffix
     * @return LinkedList<TV|TVI>
     */
    public function appendedAll(iterable $suffix): LinkedList
    {
        return LinkedList::collect(Ops\AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return LinkedList<TV|TVI>
     */
    public function prepended(mixed $elem): LinkedList
    {
        return new Cons($elem, $this);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $prefix
     * @return LinkedList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): LinkedList
    {
        return LinkedList::collect(Ops\PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return LinkedList::collect(Ops\FilterOperation::of($this->getIterator())($predicate));
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
        return LinkedList::collect(Ops\FilterMapOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     * @return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return LinkedList::collect(Ops\FilterNotNullOperation::of($this->getIterator())());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return LinkedList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): LinkedList
    {
        return LinkedList::collect(Ops\FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
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
        return LinkedList::collect(Ops\FlatMapOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return LinkedList::collect(Ops\TakeWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return LinkedList::collect(Ops\DropWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function take(int $length): LinkedList
    {
        return LinkedList::collect(Ops\TakeOperation::of($this->getIterator())($length));
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function drop(int $length): LinkedList
    {
        return LinkedList::collect(Ops\DropOperation::of($this->getIterator())($length));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return LinkedList<TV>
     */
    public function tap(callable $callback): LinkedList
    {
        Stream::emits(Ops\TapOperation::of($this->getIterator())($callback))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV, TV): int $cmp
     * @return LinkedList<TV>
     */
    public function sorted(callable $cmp): LinkedList
    {
        return LinkedList::collect(Ops\SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return LinkedList<TV|TVI>
     */
    public function intersperse(mixed $separator): LinkedList
    {
        return LinkedList::collect(Ops\IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $that
     * @return LinkedList<array{TV, TVI}>
     */
    public function zip(iterable $that): LinkedList
    {
        return LinkedList::collect(Ops\ZipOperation::of($this->getIterator())($that));
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->mkString('LinkedList(', ', ', ')');
    }
}
