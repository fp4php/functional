<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Functional\Semigroup\Semigroup;

/**
 * @template A
 * @psalm-immutable
 * @extends Semigroup<A>
 */
abstract class Monoid extends Semigroup
{
    /**
     * @psalm-return A
     */
    abstract public function empty(): mixed;
}
