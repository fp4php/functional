<?php

declare(strict_types=1);

namespace Fp\Collections;

use Countable;
use Iterator;
use IteratorAggregate;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends IteratorAggregate<TK, TV>
 */
interface Collection extends IteratorAggregate, Countable
{
    /**
     * {@inheritDoc}
     *
     * @return Iterator<TK, TV>
     */
    public function getIterator(): Iterator;

    public function toString(): string;

    public function __toString(): string;
}
