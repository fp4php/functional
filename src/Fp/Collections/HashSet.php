<?php

declare(strict_types=1);

namespace Fp\Collections;

use Generator;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @extends AbstractSet<TV>
 */
final class HashSet extends AbstractSet
{
    /**
     * @param HashMap<TV, TV> $map
     */
    private function __construct(private HashMap $map)
    {
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $pairs = LinkedList::collect($source)->map(fn(mixed $elem) => [$elem, $elem]);

        /**
         * Inference isn't working in generic context
         * @var self<TVI>
         */
        return new self(HashMap::collect($pairs));
    }

    /**
     * @inheritDoc
     * @return Generator<int, TV>
     */
    public function getIterator(): Generator
    {
        return $this->map->generateValues();
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
        $source = function (): Generator {
            $toggle = true;

            foreach ($this as $elem) {
                if ($toggle) {
                    $toggle = false;
                    continue;
                }

                yield $elem;
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return new self($this->map->filter(fn(Entry $e) => $predicate($e->value)));
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
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                $result = $callback($element);

                foreach ($result as $item) {
                    yield $item;
                }
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        $source = function () use ($callback): Generator {
            foreach ($this as $element) {
                yield $callback($element);
            }
        };

        return self::collect($source());
    }
}
