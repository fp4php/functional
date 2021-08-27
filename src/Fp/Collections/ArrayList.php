<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Fp\Functional\Option\Option;
use Generator;
use Iterator;

use function Fp\of;

/**
 * O(1) {@see Seq::at()} and {@see IndexedSeq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractIndexedSeq<TV>
 */
final class ArrayList extends AbstractIndexedSeq
{
    /**
     * @param list<TV> $elements
     */
    public function __construct(public array $elements)
    {
    }

    /**
     * @inheritDoc
     * @psalm-pure
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $buffer = [];

        foreach ($source as $elem) {
            $buffer[] = $elem;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return new self([...$this->elements, $elem]);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        $source = function() use ($suffix): Generator {
            foreach ($this->elements as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collect($source());
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return new self([$elem, ...$this->elements]);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        $source = function() use ($prefix): Generator {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this->elements as $suffixElem) {
                yield $suffixElem;
            }
        };

        return self::collect($source());
    }

    /**
     * O(1) time/space complexity
     *
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return Option::fromNullable($this->elements[$index] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                $buffer[] = $element;
            }
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return $this->filter(fn(mixed $v) => !is_null($v));
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
        /** @var self<TVO> */
        return $this->filter(fn(mixed $v): bool => of($v, $fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            $result = $callback($element);

            foreach ($result as $item) {
                $buffer[] =  $item;
            }
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        return Option::fromNullable($this->elements[0] ?? null);
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            $buffer[] = $callback($element);
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->elements));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        $buffer = $this->toArray();
        array_shift($buffer);
        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        $pairs = $this->map(function($elem) use ($callback) {
            /** @var TV $elem */
            return [$callback($elem), $elem];
        });

        return self::collect(HashMap::collect($pairs)->values());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function takeWhile(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if (!$predicate($element)) {
                break;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function dropWhile(callable $predicate): self
    {
        $buffer = [];

        foreach ($this->elements as $element) {
            if ($predicate($element)) {
                continue;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function take(int $length): self
    {
        $buffer = [];

        foreach ($this->elements as $i => $element) {
            if ($i === $length) {
                break;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function drop(int $length): self
    {
        $buffer = [];

        foreach ($this->elements as $i => $element) {
            if ($i < $length) {
                continue;
            }

            $buffer[] = $element;
        }

        return new self($buffer);
    }
}
