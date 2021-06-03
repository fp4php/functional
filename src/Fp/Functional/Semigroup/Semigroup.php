<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template A
 * @psalm-immutable
 */
interface Semigroup
{
    /**
     * @return A
     */
    public function extract(): mixed;

    /**
     * @psalm-param A $rhs
     * @psalm-return A
     */
    public function combineOne(mixed $rhs): mixed;

    /**
     * @psalm-param Semigroup<A> $rhs
     * @psalm-return Semigroup<A>
     */
    public function combineOneSemi(Semigroup $rhs): Semigroup;
}
