<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Semigroup\Semigroup;

/**
 * @template A
 * @psalm-immutable
 * @extends Validated<empty, A>
 */
final class Valid extends Validated
{
    /**
     * @psalm-param A $value
     * @psalm-param Semigroup<A> $semi
     */
    public function __construct(protected Semigroup $semi)
    {
    }

    /**
     * @psalm-return A
     */
    public function get(): mixed
    {
        return $this->semi->get();
    }
}
