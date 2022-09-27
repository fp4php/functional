<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Functional\WithExtensions;
use Fp\Operations as Ops;
use Fp\Operations\FoldOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Callable\dropFirstArg;
use function Fp\Callable\toSafeClosure;
use function Fp\Cast\fromPairs;
use function Fp\Collection\at;
use function Fp\Collection\keys;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;

/**
 * O(1) {@see Seq::at()} and {@see Seq::__invoke} operations
 *
 * @template-covariant TV
 * @implements Seq<TV>
 *
 * @psalm-seal-methods
 * @mixin ArrayListExtensions<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class ArrayList implements Seq
{
    use WithExtensions;

    /**
     * @param list<TV> $elements
     */
    public function __construct(public array $elements) { }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $source
     * @return ArrayList<TVI>
     */
    public static function collect(iterable $source): ArrayList
    {
        $buffer = [];

        foreach ($source as $elem) {
            $buffer[] = $elem;
        }

        return new ArrayList($buffer);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $val
     * @return ArrayList<TVI>
     */
    public static function singleton(mixed $val): ArrayList
    {
        return new ArrayList([$val]);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<never>
     */
    public static function empty(): ArrayList
    {
        return new ArrayList([]);
    }

    /**
     * {@inheritDoc}
     *
     * @param positive-int $by
     * @return ArrayList<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): ArrayList
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
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        return $this->elements;
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
     * @psalm-if-this-is ArrayList<array{TKO, TVO}>
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
     * @psalm-if-this-is ArrayList<array{TKO, TVO}>
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
        return LinkedList::collect($this);
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
        return $this;
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
     * @psalm-if-this-is ArrayList<array{TKI, TVI}>
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
     * @psalm-if-this-is ArrayList<array{TKI, TVI}>
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
     */
    public function count(): int
    {
        return count($this->elements);
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
     * {{@inheritDoc}}
     * O(1) time/space complexity
     *
     * @return Option<TV>
     */
    public function at(int $index): Option
    {
        return Option::fromNullable($this->elements[$index] ?? null);
    }

    /**
     * {@inheritDoc}
     *
     * @return Option<TV>
     */
    public function head(): Option
    {
        return Option::fromNullable($this->elements[0] ?? null);
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function tail(): ArrayList
    {
        return ArrayList::collect(Ops\TailOperation::of($this->getIterator())());
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
     * @return ArrayList<TV>
     */
    public function reverse(): ArrayList
    {
        return new ArrayList(array_reverse($this->elements));
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
     * @psalm-assert-if-true ArrayList<TVO> $this
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
     * @return Option<ArrayList<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return Ops\TraverseOptionOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is ArrayList<Option<TVO>>
     *
     * @return Option<ArrayList<TVO>>
     */
    public function sequenceOption(): Option
    {
        return Ops\TraverseOptionOperation::id($this->getIterator())
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, ArrayList<TVO>>
     */
    public function traverseEither(callable $callback): Either
    {
        return Ops\TraverseEitherOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is ArrayList<Either<E, TVO>>
     *
     * @return Either<E, ArrayList<TVO>>
     */
    public function sequenceEither(): Either
    {
        return Ops\TraverseEitherOperation::id($this->getIterator())
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Separated<ArrayList<TV>, ArrayList<TV>>
     */
    public function partition(callable $predicate): Separated
    {
        return Ops\PartitionOperation::of($this->getIterator())(dropFirstArg($predicate))
            ->mapLeft(fn($left) => ArrayList::collect($left))
            ->map(fn($right) => ArrayList::collect($right));
    }

    /**
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<ArrayList<LO>, ArrayList<RO>>
     */
    public function partitionMap(callable $callback): Separated
    {
        return Ops\PartitionMapOperation::of($this->getIterator())(dropFirstArg($callback))
            ->mapLeft(fn($left) => ArrayList::collect($left))
            ->map(fn($right) => ArrayList::collect($right));
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
        return Option::fromNullable(array_key_last($this->elements))
            ->flatMap(fn($index) => at($this->elements, $index));
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
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, NonEmptyArrayList<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        return Ops\GroupByOperation::of($this->getIterator())(dropFirstArg($callback))
            ->map(fn(NonEmptyHashMap $neSeq) => $neSeq->values()->toNonEmptyArrayList());
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
        return Ops\GroupMapOperation::of($this->getIterator())(dropFirstArg($group), dropFirstArg($map))
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
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return ArrayList<TVO>
     */
    public function map(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\MapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return ArrayList<TVO>
     */
    public function mapN(callable $callback): ArrayList
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
     * @return ArrayList<TV | TVI>
     */
    public function appended(mixed $elem): ArrayList
    {
        return ArrayList::collect(Ops\AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $suffix
     * @return ArrayList<TV | TVI>
     */
    public function appendedAll(iterable $suffix): ArrayList
    {
        return ArrayList::collect(Ops\AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return ArrayList<TV | TVI>
     */
    public function prepended(mixed $elem): ArrayList
    {
        return ArrayList::collect(Ops\PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $prefix
     * @return ArrayList<TV|TVI>
     */
    public function prependedAll(iterable $prefix): ArrayList
    {
        return ArrayList::collect(Ops\PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function filter(callable $predicate): ArrayList
    {
        return ArrayList::collect(Ops\FilterOperation::of($this->getIterator())(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return ArrayList<TVO>
     */
    public function filterMap(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\FilterMapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function filterNotNull(): ArrayList
    {
        return ArrayList::collect(Ops\FilterNotNullOperation::of($this->getIterator())());
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return ArrayList<TVO>
     */
    public function filterOf(string|array $fqcn, bool $invariant = false): ArrayList
    {
        return ArrayList::collect(Ops\FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     * @psalm-if-this-is ArrayList<iterable<TVO>|Collection<TVO>>
     *
     * @return ArrayList<TVO>
     */
    public function flatten(): ArrayList
    {
        return ArrayList::collect(Ops\FlattenOperation::of($this));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<TVO>|Collection<TVO>) $callback
     * @return ArrayList<TVO>
     */
    public function flatMap(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\FlatMapOperation::of($this->getIterator())(dropFirstArg($callback)));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function takeWhile(callable $predicate): ArrayList
    {
        return ArrayList::collect(Ops\TakeWhileOperation::of($this->getIterator())(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return ArrayList<TV>
     */
    public function dropWhile(callable $predicate): ArrayList
    {
        return ArrayList::collect(Ops\DropWhileOperation::of($this->getIterator())(dropFirstArg($predicate)));
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function take(int $length): ArrayList
    {
        return ArrayList::collect(Ops\TakeOperation::of($this->getIterator())($length));
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function drop(int $length): ArrayList
    {
        return ArrayList::collect(Ops\DropOperation::of($this->getIterator())($length));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return ArrayList<TV>
     */
    public function tap(callable $callback): ArrayList
    {
        Stream::emits(Ops\TapOperation::of($this->getIterator())(dropFirstArg($callback)))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function sorted(): ArrayList
    {
        return ArrayList::collect(Ops\SortedOperation::of($this->getIterator())->asc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return ArrayList<TV>
     */
    public function sortedBy(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\SortedOperation::of($this->getIterator())->ascBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<TV>
     */
    public function sortedDesc(): ArrayList
    {
        return ArrayList::collect(Ops\SortedOperation::of($this->getIterator())->desc());
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return ArrayList<TV>
     */
    public function sortedDescBy(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\SortedOperation::of($this->getIterator())->descBy($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return ArrayList<TV | TVI>
     */
    public function intersperse(mixed $separator): ArrayList
    {
        return ArrayList::collect(Ops\IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $that
     * @return ArrayList<array{TV, TVI}>
     */
    public function zip(iterable $that): ArrayList
    {
        return ArrayList::collect(Ops\ZipOperation::of($this->getIterator())($that));
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayList<array{int, TV}>
     */
    public function zipWithKeys(): ArrayList
    {
        return ArrayList::collect(Ops\ZipOperation::of(keys($this->getIterator()))($this->getIterator()));
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): mixed $callback
     * @return ArrayList<TV>
     */
    public function uniqueBy(callable $callback): ArrayList
    {
        return ArrayList::collect(Ops\UniqueByOperation::of($this->getIterator())($callback));
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

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => Ops\ToStringOperation::of($value))
            ->mkString('ArrayList(', ', ', ')');
    }
}
