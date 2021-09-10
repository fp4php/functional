<?php

declare(strict_types=1);

namespace Fp\Collections;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use LogicException;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamOps<TV>
 * @implements StreamCasts<TV>
 * @implements IteratorAggregate<TV>
 */
final class Stream implements StreamOps, StreamCasts, IteratorAggregate
{
    /**
     * @use StreamChainable<TV>
     */
    use StreamChainable;

    /**
     * @use StreamUnchainable<TV>
     */
    use StreamUnchainable;

    /**
     * @use StreamConvertible<TV>
     */
    use StreamConvertible;

    /**
     * @psalm-readonly-allow-private-mutation $closed
     */
    private bool $closed = false;

    /**
     * @param iterable<TV> $emitter
     */
    public function __construct(private iterable $emitter)
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
        return new self($source);
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        $this->closed = !$this->closed
            ? true
            : throw new LogicException('Can not traverse closed stream');

        return is_array($this->emitter)
            ? new ArrayIterator($this->emitter)
            : new IteratorIterator($this->emitter);
    }
}
