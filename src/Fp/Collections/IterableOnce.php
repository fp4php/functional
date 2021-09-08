<?php

declare(strict_types=1);

namespace Fp\Collections;

use Closure;
use Error;
use Generator;
use IteratorAggregate;

/**
 * Conditionally pure internal iterable
 * Can be iterated only once
 *
 * @internal
 * @template T
 * @psalm-immutable
 * @implements IteratorAggregate<T>
 */
final class IterableOnce implements IteratorAggregate
{
    /**
     * @psalm-allow-private-mutation $iterated
     * @var bool
     */
    private bool $iterated = false;

    /**
     * @psalm-param Closure(): iterable<T> $iterableThunk
     */
    public function __construct(private Closure $iterableThunk)
    {
    }

    /**
     * @psalm-pure
     * @template TI
     * @param Closure(): iterable<TI> $iterableThunk
     * @return self<TI>
     */
    public static function of(Closure $iterableThunk): self
    {
       return new self($iterableThunk);
    }

    /**
     * @return Generator<T>
     */
    public function getIterator(): Generator
    {
        if ($this->iterated) {
            throw new Error(self::class . ' must be iterated only once');
        } else {
            $this->iterated = true;
        }

        foreach (($this->iterableThunk)() as $elem) {
            yield $elem;
        }
    }
}
