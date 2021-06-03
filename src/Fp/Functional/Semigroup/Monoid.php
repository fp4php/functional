<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template A
 * @psalm-immutable
 * @extends Semigroup<A>
 */
interface Monoid extends Semigroup
{
    /**
     * @psalm-return A
     */
    public function empty(): mixed;
}
