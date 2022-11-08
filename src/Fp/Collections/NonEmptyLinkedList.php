<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Operations as Ops;
use Fp\Operations\FoldOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Callable\toSafeClosure;
use function Fp\Cast\fromPairs;

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
    /**
     * @param LinkedList<TV> $linkedList
     */
    public function __construct(private readonly LinkedList $linkedList)
    {
    }

    #region NonEmptySeqCollector

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
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Option<NonEmptyLinkedList<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $list = LinkedList::collect($source);

        return !$list->isEmpty()
            ? Option::some(new NonEmptyLinkedList($list))
            : Option::none();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
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
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<mixed, TVI> $source
     * @return NonEmptyLinkedList<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($source);
    }

    #endregion NonEmptySeqCollector

    #region NonEmptySeqChainableOps

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>>
     *
     * @return NonEmptyLinkedList<TVO>
     */
    public function flatten(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->flatten());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyLinkedList<TVO>
     */
    public function flatMap(callable $callback): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->flatMap($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptyLinkedList<TVO>
     */
    public function flatMapN(callable $callback): NonEmptyLinkedList
    {
        return $this->flatMap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function reverse(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->reverse());
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
        return new NonEmptyLinkedList($this->linkedList->map($callback));
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
            return toSafeClosure($callback)(...$tuple);
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
        return new NonEmptyLinkedList($this->linkedList->appended($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $suffix
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->appendedAll($suffix));
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
        return new NonEmptyLinkedList($this->linkedList->prepended($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $prefix
     * @return NonEmptyLinkedList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->prependedAll($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function tap(callable $callback): NonEmptyLinkedList
    {
        Stream::emits(Ops\TapOperation::of($this)(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(mixed...): void $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function tapN(callable $callback): NonEmptyLinkedList
    {
        return $this->tap(function($tuple) use ($callback) {
            /** @var array $tuple */;
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<mixed, TVI> $that
     * @return NonEmptyLinkedList<array{TV, TVI}>
     */
    public function zip(iterable $that): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->zip($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<array{int, TV}>
     */
    public function zipWithKeys(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->zipWithKeys());
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function sorted(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->sorted());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function sortedBy(callable $callback): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->sortedBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function sortedDesc(): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->sortedDesc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function sortedDescBy(callable $callback): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->sortedDescBy($callback));
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
        return new NonEmptyLinkedList($this->linkedList->intersperse($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptyLinkedList<TV>
     */
    public function uniqueBy(callable $callback): NonEmptyLinkedList
    {
        return new NonEmptyLinkedList($this->linkedList->uniqueBy($callback));
    }

    #endregion NonEmptySeqChainableOps

    #region NonEmptySeqTerminalOps

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return $this->linkedList->filter($predicate);
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
        return $this->linkedList->filterMap($callback);
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
     *
     * @return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return $this->linkedList->filterNotNull();
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
        return $this->linkedList->filterOf($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return $this->linkedList->tail();
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function init(): LinkedList
    {
        return $this->linkedList->init();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return $this->linkedList->takeWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return $this->linkedList->dropWhile($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function take(int $length): LinkedList
    {
        return $this->linkedList->take($length);
    }

    /**
     * {@inheritDoc}
     *
     * @return LinkedList<TV>
     */
    public function drop(int $length): LinkedList
    {
        return $this->linkedList->drop($length);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function at(int $index): Option
    {
        return Ops\AtOperation::of($this)($index);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return Ops\EveryOperation::of($this)(dropFirstArg($predicate));
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
     * @psalm-assert-if-true NonEmptyLinkedList<TVO> $this
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function everyOf(string|array $fqcn, bool $invariant = false): bool
    {
        return Ops\EveryOfOperation::of($this)($fqcn, $invariant);
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
        return $this->linkedList
            ->traverseOption($callback)
            ->map(fn($list) => new NonEmptyLinkedList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<NonEmptyLinkedList<TVO>>
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
     * @psalm-if-this-is NonEmptyLinkedList<Option<TVO>>
     *
     * @return Option<NonEmptyLinkedList<TVO>>
     */
    public function sequenceOption(): Option
    {
        return $this->linkedList
            ->sequenceOption()
            ->map(fn($list) => new NonEmptyLinkedList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, NonEmptyLinkedList<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return $this->linkedList
            ->traverseEither($callback)
            ->map(fn($list) => new NonEmptyLinkedList($list));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, NonEmptyLinkedList<TVO>>
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
     * @param callable(TV): bool $predicate
     * @return Separated<LinkedList<TV>, LinkedList<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return Ops\PartitionOperation::of($this)(dropFirstArg($predicate))
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
        return Ops\PartitionMapOperation::of($this)(dropFirstArg($callback))
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
     * @return Separated<LinkedList<LO>, LinkedList<RO>>
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
     * @template E
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<Either<E, TVO>>
     *
     * @return Either<E, NonEmptyLinkedList<TVO>>
     */
    public function sequenceEither(): Either
    {
        return $this->linkedList
            ->sequenceEither()
            ->map(fn($list) => new NonEmptyLinkedList($list));
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
        return new NonEmptyHashMap(Ops\GroupMapReduceOperation::of($this)(dropFirstArg($group), dropFirstArg($map), $reduce));
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
            HashMap::collect(Ops\ReindexOperation::of($this)(dropFirstArg($callback))),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return NonEmptyHashMap<TKO, TV>
     */
    public function reindexN(callable $callback): NonEmptyMap
    {
        return $this->reindex(function($tuple) use ($callback) {
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
        return Ops\ExistsOperation::of($this)(dropFirstArg($predicate));
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
        return Ops\ExistsOfOperation::of($this)($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return Ops\FirstOperation::of($this)(dropFirstArg($predicate));
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
        return Ops\FirstOfOperation::of($this)($fqcn, $invariant);
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
        return Ops\LastOfOperation::of($this)($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function head(): mixed
    {
        return $this->linkedList->head()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return Ops\LastOperation::of($this)(dropFirstArg($predicate));
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
        return Ops\LastOperation::of($this)()->getUnsafe();
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
     * @return NonEmptyMap<TKO, NonEmptyLinkedList<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap
    {
        $groups = Ops\GroupMapOperation::of($this)(dropFirstArg($group), dropFirstArg($map));

        return (new NonEmptyHashMap($groups))
            ->map(fn(NonEmptyHashMap $elem) => $elem->values()->toNonEmptyLinkedList());
    }

    /**
     * {@inheritDoc}
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return Ops\MkStringOperation::of($this)($start, $sep, $end);
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function max(): mixed
    {
        return $this->linkedList->max()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function maxBy(callable $callback): mixed
    {
        return $this->linkedList->maxBy($callback)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function min(): mixed
    {
        return $this->linkedList->min()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function minBy(callable $callback): mixed
    {
        return $this->linkedList->minBy($callback)->getUnsafe();
    }

    #endregion NonEmptySeqTerminalOps

    #region NonEmptySeqCastableOps

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
        return $this->linkedList;
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
        return new NonEmptyArrayList($this->toArrayList());
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

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array
    {
        return $this->linkedList->toMergedArray();
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyLinkedList<array<TKO, TVO>>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyMergedArray(): array
    {
        return $this->linkedList->toNonEmptyMergedArray()->getUnsafe();
    }

    public function toString(): string
    {
        return (string) $this;
    }

    #endregion NonEmptySeqCastableOps

    #region Traversable

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->linkedList);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->linkedList->count();
    }

    #endregion Traversable

    #region Magic methods

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return NonEmptyLinkedListExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return NonEmptyLinkedListExtensions::callStatic($name, $arguments);
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->toLinkedList()
            ->mkString('NonEmptyLinkedList(', ', ', ')');
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

    #endregion Magic methods
}
