<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 * @implements Map<TK, TV>
 */
abstract class AbstractMap implements Map
{
    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    abstract public function getIterator(): Iterator;
}
