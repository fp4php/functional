<?php

declare(strict_types=1);

namespace Fp\Collections;

use Countable;
use Iterator;
use IteratorAggregate;

/**
 * @template-covariant TV
 * @implements IteratorAggregate<empty, TV>
 */
interface NonEmptyCollection extends IteratorAggregate, Countable
{
    /**
     * {@inheritDoc}
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;

    public function toString(): string;

    public function __toString(): string;
}
