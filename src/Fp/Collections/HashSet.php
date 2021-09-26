<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Operations\CountOperation;
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
use Fp\Operations\HeadOperation;
use Fp\Operations\LastOperation;
use Fp\Operations\MapValuesOperation;
use Fp\Functional\Option\Option;
use Fp\Operations\ReduceOperation;
use Fp\Operations\TailOperation;
use Fp\Operations\TapOperation;
use Fp\Streams\Stream;
use Generator;
use Iterator;

use function Fp\Callable\asGenerator;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @implements Set<TV>
 */
final class HashSet implements Set
{
    /**
     * @param HashMap<TV, TV> $map
     */
    private function __construct(private HashMap $map)
    {
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        return new self(ArrayList::collect($source)->toHashMap(fn(mixed $elem) => [$elem, $elem]));
    }

    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator
    {
        return asGenerator(function () {
            foreach ($this->map as $pair) {
                yield $pair[1];
            }
        });
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
     * @return list<TV>
     */
    public function toArray(): array
    {
        $buffer = [];

        foreach ($this as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
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
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
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
        return EveryOperation::of($this)($predicate);
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
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return FoldOperation::of($this->iter())($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        return ReduceOperation::of($this->iter())($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        return HeadOperation::of($this->iter())();
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
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option
    {
        return FirstOperation::of($this->iter())();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option
    {
        return LastOperation::of($this->iter())();
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

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->map->get($element)->isNonEmpty();
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $element
     * @return self<TV|TVI>
     */
    public function updated(mixed $element): self
    {
        return new self($this->map->updated($element, $element));
    }

    /**
     * @inheritDoc
     * @param TV $element
     * @return self<TV>
     */
    public function removed(mixed $element): self
    {
        return new self($this->map->removed($element));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        return self::collect(TailOperation::of($this->iter())());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return self::collect(FilterOperation::of($this->iter())($predicate));
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
        return self::collect(FilterOfOperation::of($this->iter())($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->iter())());
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(FilterMapOperation::of($this->iter())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collect(FlatMapOperation::of($this->iter())($callback));
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapValuesOperation::of($this)($callback));
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

    public function isEmpty():bool
    {
        return $this->map->isEmpty();
    }
}
