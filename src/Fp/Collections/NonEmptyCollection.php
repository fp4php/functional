<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;
use IteratorAggregate;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements IteratorAggregate<empty, TV>
 */
interface NonEmptyCollection extends IteratorAggregate
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
