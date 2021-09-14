<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Iterator;

use function Fp\Callable\asGenerator;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @extends AbstractOrderedSet<TV>
 */
final class HashSet extends AbstractOrderedSet
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
        return $this->map->generateValues();
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
        return self::collect(asGenerator(function () {
            $toggle = true;

            foreach ($this as $elem) {
                if ($toggle) {
                    $toggle = false;
                    continue;
                }

                yield $elem;
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return new self($this->map->filter(function (Entry $e) use ($predicate) {
            /** @psalm-var TV $value */
            $value = $e->value;
            return $predicate($value);
        }));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return $this->filter(fn($elem) => null !== $elem);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(asGenerator(function () use ($callback) {
            foreach ($this as $element) {
                $result = $callback($element);

                if ($result->isSome()) {
                    yield $result->get();
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collect(asGenerator(function () use ($callback) {
            foreach ($this as $element) {
                $result = $callback($element);

                foreach ($result as $item) {
                    yield $item;
                }
            }
        }));
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(asGenerator(function () use ($callback) {
            foreach ($this as $element) {
                yield $callback($element);
            }
        }));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
     */
    public function tap(callable $callback): self
    {
        foreach ($this as $elem) {
            $callback($elem);
        }

        return $this;
    }

    public function isEmpty():bool
    {
        return $this->map->isEmpty();
    }
}
