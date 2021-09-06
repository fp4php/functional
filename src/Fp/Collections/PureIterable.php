<?php

declare(strict_types=1);

namespace Fp\Collections;

use Closure;
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
 */
final class PureIterable implements IteratorAggregate
{
    /**
     * @psalm-allow-private-mutation $drained
     * @var bool
     */
    private bool $drained = false;

    /**
     * @psalm-param Closure(): iterable<T> $emitter
     */
    public function __construct(private Closure $emitter)
    {
    }

    /**
     * @psalm-pure
     * @template TI
     * @param Closure(): iterable<TI> $emitter
     * @return self<TI>
     */
    public static function of(Closure $emitter): self
    {
       return new self($emitter);
    }

    /**
     * @return Generator<T>
     */
    public function getIterator(): Generator
    {
        if ($this->drained) {
            throw new Error(self::class . ' is not pure');
        } else {
            $this->drained = true;
        }

        foreach (($this->emitter)() as $elem) {
            yield $elem;
        }
    }
}
