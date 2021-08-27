<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
abstract class AbstractNonEmptySeq implements NonEmptySeq
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    abstract public function getIterator(): Iterator;

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        return $this->last(fn() => true)->getUnsafe();
    }
}
