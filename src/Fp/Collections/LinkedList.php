<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use Fp\Operations as Ops;
use Fp\Functional\Option\Option;
use Fp\Operations\FoldOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Callable\toSafeClosure;
use function Fp\Cast\asList;
use function Fp\Cast\fromPairs;
use function Fp\Collection\keys;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;
use function Fp\Evidence\proveOf;

/**
 * O(1) {@see Seq::prepended} operation
 * Fast {@see Seq::reverse} operation
 *
 * @template-covariant TV
 * @implements Seq<TV>
 *
 * @psalm-seal-methods
 * @mixin LinkedListExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
abstract class LinkedList implements Seq
{
    #region SeqCollector

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $source
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
     * @return LinkedList<empty>
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

    #endregion SeqCollector

    #region SeqCastableOps

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
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is LinkedList<array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array
    {
        return array_merge(...$this->toList());
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is LinkedList<array<TKO, TVO>>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     */
    public function toNonEmptyMergedArray(): Option
    {
        return proveNonEmptyArray($this->toMergedArray());
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion SeqCastableOps

    #region SeqChainableOps

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
     * @return LinkedList<TV>
     */
    public function init(): LinkedList
    {
        return LinkedList::collect(Ops\InitOperation::of($this->getIterator())());
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
        return LinkedList::collect(Ops\MapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return LinkedList<TVO>
     */
    public function mapN(callable $callback): LinkedList
    {
        return $this->map(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
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
     * @param (iterable<TVI>|Collection<TVI>) $suffix
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
     * @param (iterable<TVI>|Collection<TVI>) $prefix
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
        return LinkedList::collect(Ops\FilterOperation::of($this->getIterator())(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return LinkedList<TV>
     */
    public function filterN(callable $predicate): LinkedList
    {
        return $this->filter(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
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
        return LinkedList::collect(Ops\FilterMapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return LinkedList<TVO>
     */
    public function filterMapN(callable $callback): LinkedList
    {
        return $this->filterMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
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
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return LinkedList<TVO>
     */
    public function filterOf(string|array $fqcn, bool $invariant = false): LinkedList
    {
        return LinkedList::collect(Ops\FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is LinkedList<iterable<TVO>|Collection<TVO>>
     *
     * @return LinkedList<TVO>
     */
    public function flatten(): LinkedList
    {
        return LinkedList::collect(Ops\FlattenOperation::of($this));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<TVO>|Collection<TVO>) $callback
     * @return LinkedList<TVO>
     */
    public function flatMap(callable $callback): LinkedList
    {
        return LinkedList::collect(Ops\FlatMapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): (iterable<TVO>|Collection<TVO>) $callback
     * @return LinkedList<TVO>
     */
    public function flatMapN(callable $callback): LinkedList
    {
        return $this->flatMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return LinkedList::collect(Ops\TakeWhileOperation::of($this->getIterator())(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return LinkedList::collect(Ops\DropWhileOperation::of($this->getIterator())(dropFirstArg($predicate)));
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
        Stream::emits(Ops\TapOperation::of($this->getIterator())(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return LinkedList<TV>
     */
    public function tapN(callable $callback): LinkedList
    {
        return $this->tap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function sorted(): LinkedList
    {
        return LinkedList::collect(Ops\SortedOperation::of($this->getIterator())->asc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return LinkedList<TV>
     */
    public function sortedBy(callable $callback): LinkedList
    {
        return LinkedList::collect(Ops\SortedOperation::of($this->getIterator())->ascBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function sortedDesc(): LinkedList
    {
        return LinkedList::collect(Ops\SortedOperation::of($this->getIterator())->desc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return LinkedList<TV>
     */
    public function sortedDescBy(callable $callback): LinkedList
    {
        return LinkedList::collect(Ops\SortedOperation::of($this->getIterator())->descBy($callback));
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
     * @param (iterable<TVI>|Collection<TVI>) $that
     * @return LinkedList<array{TV, TVI}>
     */
    public function zip(iterable $that): LinkedList
    {
        return LinkedList::collect(Ops\ZipOperation::of($this->getIterator())($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<array{int, TV}>
     */
    public function zipWithKeys(): LinkedList
    {
        return LinkedList::collect(Ops\ZipOperation::of(keys($this->getIterator()))($this->getIterator()));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return LinkedList<TV>
     */
    public function uniqueBy(callable $callback): LinkedList
    {
        return LinkedList::collect(Ops\UniqueByOperation::of($this->getIterator())($callback));
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

    #endregion SeqChainableOps

    #region SeqTerminal


    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return !($this instanceof Cons);
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
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool
    {
        return $this->every(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-assert-if-true LinkedList<TVO> $this
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
     * @return Option<LinkedList<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<LinkedList<TVO>>
     */
    public function traverseOptionN(callable $callback): Option
    {
        return $this->traverseOption(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
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
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, LinkedList<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return Ops\TraverseEitherOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, LinkedList<TVO>>
     */
    public function traverseEitherN(callable $callback): Either
    {
        return $this->traverseEither(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is LinkedList<Either<E, TVO>>
     *
     * @return Either<E, LinkedList<TVO>>
     */
    public function sequenceEither(): Either
    {
        return Ops\TraverseEitherOperation::id($this->getIterator())
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<LinkedList<TV>, LinkedList<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return Ops\PartitionOperation::of($this->getIterator())(dropFirstArg($predicate))
            ->mapLeft(fn($left) => LinkedList::collect($left))
            ->map(fn($right) => LinkedList::collect($right));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<LinkedList<TV>, LinkedList<TV>>
     */
    public function partitionN(callable $predicate): Separated
    {
        return $this->partition(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<LinkedList<LO>, LinkedList<RO>>
     */
    public function partitionMap(callable $callback): Separated
    {
        return Ops\PartitionMapOperation::of($this->getIterator())(dropFirstArg($callback))
            ->mapLeft(fn($left) => LinkedList::collect($left))
            ->map(fn($right) => LinkedList::collect($right));
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<Seq<LO>, Seq<RO>>
     */
    public function partitionMapN(callable $callback): Separated
    {
        return $this->partitionMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
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
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool
    {
        return $this->exists(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function existsOf(string|array $fqcn, bool $invariant = false): bool
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
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option
    {
        return $this->first(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
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
     * @return Option<TV>
     */
    public function head(): Option
    {
        return proveOf($this, Cons::class)->map(fn(Cons $cons): mixed => $cons->head);
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
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option
    {
        return $this->last(function($tuple) use ($predicate) {
            /** @var array $tuple */;
            return toSafeClosure($predicate)(...$tuple);
        });
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
        return Ops\GroupByOperation::of($this->getIterator())(dropFirstArg($callback))
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
     * @return HashMap<TKO, NonEmptyLinkedList<TVO>>
     */
    public function groupMap(callable $group, callable $map): HashMap
    {
        return Ops\GroupMapOperation::of($this->getIterator())(dropFirstArg($group), dropFirstArg($map))
            ->map(fn(NonEmptyHashMap $hs) => $hs->values()->toNonEmptyLinkedList());
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
        return Ops\GroupMapReduceOperation::of($this->getIterator())(dropFirstArg($group), dropFirstArg($map), $reduce);
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
        return HashMap::collect(Ops\ReindexOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return HashMap<TKO, TV>
     */
    public function reindexN(callable $callback): HashMap
    {
        return $this->reindex(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function max(): Option
    {
        return Ops\MaxElementOperation::of($this->getIterator())();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function maxBy(callable $callback): Option
    {
        return Ops\MaxByElementOperation::of($this->getIterator())($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function min(): Option
    {
        return Ops\MinElementOperation::of($this->getIterator())();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function minBy(callable $callback): Option
    {
        return Ops\MinByElementOperation::of($this->getIterator())($callback);
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->mkString('LinkedList(', ', ', ')');
    }

    #endregion SeqTerminal

    #endregion SeqTerminalOps

    #region Extension

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return LinkedListExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return LinkedListExtensions::callStatic($name, $arguments);
    }

    #endregion Extension
}
