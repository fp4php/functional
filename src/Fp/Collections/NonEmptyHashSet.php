<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\CountOperation;
use Fp\Operations\EveryOfOperation;
use Fp\Operations\EveryOperation;
use Fp\Operations\ExistsOfOperation;
use Fp\Operations\ExistsOperation;
use Fp\Operations\FirstOfOperation;
use Fp\Operations\FirstOperation;
use Fp\Operations\HeadOperation;
use Fp\Operations\LastOperation;
use Fp\Operations\MapValuesOperation;
use Fp\Operations\ReduceOperation;
use Fp\Operations\TapOperation;
use Fp\Streams\Stream;
use Generator;
use Iterator;

use function Fp\Callable\asGenerator;
use function Fp\Cast\asNonEmptyList;
use function Fp\of;

/**
 * @template-covariant TV
 * @psalm-immutable
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
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        $hashset = HashSet::collect($source);
        return Option::condLazy(!$hashset->isEmpty(), fn() => new self($hashset));
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
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator
    {
        return $this->set->getIterator();
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
        return CountOperation::of($this->iter())();
    }

    /**
     * @inheritDoc
     * @return non-empty-list<TV>
     */
    public function toArray(): array
    {
        return asNonEmptyList($this->iter())->getUnsafe();
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
        return ArrayList::collect($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this);
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return $this->set;
    }

    /**
     * @inheritDoc
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return $this;
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
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(callable $callback): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsUnsafe(asGenerator(function () use ($callback) {
            foreach ($this as $elem) {
                yield $callback($elem);
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->iter())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return EveryOfOperation::of($this->iter())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return ExistsOperation::of($this->iter())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return ExistsOfOperation::of($this->iter())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return FirstOperation::of($this->iter())($predicate);
    }


    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return LastOperation::of($this->iter())($predicate);
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
        return FirstOfOperation::of($this->iter())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed
    {
        return ReduceOperation::of($this->iter())($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return HeadOperation::of($this->iter())()->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function firstElement(): mixed
    {
        return FirstOperation::of($this->iter())()->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        return LastOperation::of($this->iter())()->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->set->contains($element);
    }

    /**
     * @inheritDoc
     * @psalm-return HashSet<TV>
     */
    public function tail(): HashSet
    {
        return $this->set->tail();
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $element
     * @return self<TV|TVI>
     */
    public function updated(mixed $element): self
    {
        return new self($this->set->updated($element));
    }

    /**
     * @inheritDoc
     * @param TV $element
     * @return HashSet<TV>
     */
    public function removed(mixed $element): HashSet
    {
        return $this->set->removed($element);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return HashSet<TV>
     */
    public function filter(callable $predicate): HashSet
    {
        return $this->set->filter($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return HashSet<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): HashSet
    {
        return $this->set->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return HashSet<TVO>
     */
    public function filterMap(callable $callback): HashSet
    {
        return $this->set->filterMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return HashSet<TV>
     */
    public function filterNotNull(): HashSet
    {
        return $this->filter(fn($elem) => null !== $elem);
    }

    /**
     * @inheritDoc
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
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return HashSet<TVO>
     */
    public function flatMap(callable $callback): HashSet
    {
        return $this->set->flatMap($callback);
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
}
