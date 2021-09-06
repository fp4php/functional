<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Generator;
use IteratorAggregate;

/**
 * Conditionally pure internal generator
 *
 * @internal
 * @template T
 * @psalm-immutable
 * @implements IteratorAggregate<T>
 * @psalm-suppress ImpureVariable, ImpureFunctionCall, ImpureMethodCall
 */
final class PureGenerator implements IteratorAggregate
{
    /**
     * @psalm-allow-private-mutation $drained
     * @var bool
     */
    private bool $drained = false;

    /**
     * @psalm-param Generator<T> $emitter
     */
    public function __construct(private Generator $emitter)
    {
    }

    /**
     * @psalm-pure
     * @template TI
     * @param callable(): Generator<TI> $emitter
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
