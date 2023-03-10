<?php

declare(strict_types=1);

namespace Fp\Collections;

use Countable;
use IteratorAggregate;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends IteratorAggregate<TK, TV>
 */
interface Collection extends IteratorAggregate, Countable
{
    public function toString(): string;

    public function __toString(): string;
}
