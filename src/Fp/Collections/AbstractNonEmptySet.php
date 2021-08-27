<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySet<TV>
 */
abstract class AbstractNonEmptySet implements NonEmptySet
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    abstract public function getIterator(): Iterator;
}
