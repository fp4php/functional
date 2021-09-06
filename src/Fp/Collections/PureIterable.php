<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Generator;
use IteratorAggregate;

/**
 * Conditionally pure internal iterable
 *
 * @internal
 * @template T
 * @psalm-immutable
 * @implements IteratorAggregate<T>
 * @psalm-suppress ImpureVariable, ImpureFunctionCall, ImpureMethodCall
 */
final class PureIterable implements IteratorAggregate
{
    /**
     * @psalm-allow-private-mutation $drained
     * @var bool
     */
    private bool $drained = false;

    /**
     * @psalm-param iterable<T> $emitter
     */
    public function __construct(private iterable $emitter)
    {
    }

    /**
     * @psalm-pure
     * @template TI
     * @param callable(): iterable<TI> $emitter
     * @return self<TI>
     */
    public static function of(callable $emitter): self
    {
       return new self($emitter());
    }

    /**
     * @psalm-pure
     * @return list<T>
     */
    public function toList(): array
    {
        $buffer = [];

        foreach ($this as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * @psalm-pure
     * @return LinkedList<T>
     */
    public function toLinkedList(): LinkedList
    {
        $buffer = new LinkedListBuffer();

        foreach ($this as $elem) {
            $buffer->append($elem);
        }

        return $buffer->toLinkedList();
    }

    /**
     * @psalm-pure
     * @return Generator<T>
     */
    public function getIterator(): Generator
    {
        if ($this->drained) {
            throw new Error(self::class . ' is already drained');
        }

        foreach ($this->emitter as $elem) {
            yield $elem;
        }

        $this->drained = true;
    }
}
