<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\CountOperation;
use Fp\Operations\MapWithKeyOperation;
use Fp\Operations\MapOperation;
use Fp\Operations\ReindexOperation;
use Fp\Operations\ReindexWithKeyOperation;
use Fp\Operations\ToStringOperation;
use Fp\Operations\TraverseOptionOperation;
use Fp\Operations\EveryOfOperation;
use Fp\Operations\EveryOperation;
use Fp\Operations\ExistsOfOperation;
use Fp\Operations\ExistsOperation;
use Fp\Operations\FirstOfOperation;
use Fp\Operations\FirstOperation;
use Fp\Operations\HeadOperation;
use Fp\Operations\LastOperation;
use Fp\Operations\ReduceOperation;
use Fp\Operations\TapOperation;
use Fp\Streams\Stream;
use Iterator;

use function Fp\Cast\asNonEmptyList;

/**
 * @template-covariant TV
 * @psalm-suppress InvalidTemplateParam
 * @implements NonEmptySet<TV>
 */
final class NonEmptyHashSet implements NonEmptySet
{
    /**
     * @internal
     * @param HashSet<TV> $set
     */
    public function __construct(private HashSet $set)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return Option<NonEmptyHashSet<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return Option::some(HashSet::collect($source))
            ->filter(fn($hs) => !$hs->isEmpty())
            ->map(fn($hs) => new NonEmptyHashSet($hs));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return NonEmptyHashSet<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyHashSet
    {
        return NonEmptyHashSet::collect($source)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI>|NonEmptyCollection<TVI> $source
     * @return NonEmptyHashSet<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($source);
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return $this->set->getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return CountOperation::of($this->getIterator())();
    }

    /**
     * {@inheritDoc}
     *
     * @return list<TV>
     */
    public function toList(): array
    {
        return asNonEmptyList($this->getIterator())->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array
    {
        /** @var non-empty-list */
        return $this->toList();
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
        return NonEmptyLinkedList::collectUnsafe($this);
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
        return $this->set;
    }

    /**
     * {@inheritDoc}
     *
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptyHashSet<array{TKI, TVI}>
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
     * @psalm-if-this-is NonEmptyHashSet<array{TKI, TVI}>
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
     * @param TV $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * {@inheritDoc}
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getIterator())($predicate);
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
        return EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptyHashSet<TVO>>
     */
    public function traverseOption(callable $callback): Option
    {
        return TraverseOptionOperation::of($this->getIterator())($callback)
            ->map(fn($gen) => NonEmptyHashSet::collectUnsafe($gen));
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
            HashMap::collect(ReindexOperation::of($this->getIterator())($callback)),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @template TKO
     *
     * @param callable(int, TV): TKO $callback
     * @return NonEmptyHashMap<TKO, TV>
     */
    public function reindexKV(callable $callback): NonEmptyHashMap
    {
        return new NonEmptyHashMap(
            HashMap::collect(ReindexWithKeyOperation::of($this->getIterator())($callback)),
        );
    }

    /**
     * {@inheritDoc}
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return ExistsOperation::of($this->getIterator())($predicate);
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
        return ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return FirstOperation::of($this->getIterator())($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return LastOperation::of($this->getIterator())($predicate);
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
        return FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TA
     *
     * @param callable(TV|TA, TV): (TV|TA) $callback
     * @return (TV|TA)
     */
    public function reduce(callable $callback): mixed
    {
        return ReduceOperation::of($this->getIterator())($callback)->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function head(): mixed
    {
        return HeadOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function firstElement(): mixed
    {
        return FirstOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @return TV
     */
    public function lastElement(): mixed
    {
        return LastOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->set->contains($element);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function tail(): HashSet
    {
        return $this->set->tail();
    }

    /**
     * {@inheritDoc}
     *
     * @template TVI
     *
     * @param TVI $element
     * @return NonEmptyHashSet<TV|TVI>
     */
    public function updated(mixed $element): NonEmptyHashSet
    {
        return new self($this->set->updated($element));
    }

    /**
     * {@inheritDoc}
     *
     * @param TV $element
     * @return HashSet<TV>
     */
    public function removed(mixed $element): HashSet
    {
        return $this->set->removed($element);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): bool $predicate
     * @return HashSet<TV>
     */
    public function filter(callable $predicate): HashSet
    {
        return $this->set->filter($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(int, TV): bool $predicate
     * @return HashSet<TV>
     */
    public function filterKV(callable $predicate): HashSet
    {
        return $this->set->filterKV($predicate);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return HashSet<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): HashSet
    {
        return $this->set->filterOf($fqcn, $invariant);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return HashSet<TVO>
     */
    public function filterMap(callable $callback): HashSet
    {
        return $this->set->filterMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @return HashSet<TV>
     */
    public function filterNotNull(): HashSet
    {
        return $this->filter(fn($elem) => null !== $elem);
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function map(callable $callback): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe(MapOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(int, TV): TVO $callback
     * @return NonEmptyHashSet<TVO>
     */
    public function mapKV(callable $callback): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe(MapWithKeyOperation::of($this->getIterator())($callback));
    }

    /**
     * {@inheritDoc}
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<TVO>) $callback
     * @return HashSet<TVO>
     */
    public function flatMap(callable $callback): HashSet
    {
        return $this->set->flatMap($callback);
    }

    /**
     * {@inheritDoc}
     *
     * @param callable(TV): void $callback
     * @return NonEmptyHashSet<TV>
     */
    public function tap(callable $callback): NonEmptyHashSet
    {
        Stream::emits(TapOperation::of($this->getIterator())($callback))->drain();
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function subsetOf(Set|NonEmptySet $superset): bool
    {
        $isSubset = true;

        foreach ($this as $elem) {
            if (!$superset($elem)) {
                $isSubset = false;
                break;
            }
        }

        return $isSubset;
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function intersect(Set|NonEmptySet $that): Set
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ $that($elem));
    }

    /**
     * {@inheritDoc}
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function diff(Set|NonEmptySet $that): Set
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ !$that($elem));
    }

    public function __toString(): string
    {
        return $this
            ->map(fn($value) => ToStringOperation::of($value))
            ->toArrayList()
            ->mkString('NonEmptyHashSet(', ', ', ')');
    }
}
