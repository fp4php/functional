<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements Set<TV>
 */
abstract class AbstractSet implements Set
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    abstract public function getIterator(): Iterator;
}
