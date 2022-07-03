<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Functional\Validated\Validated;

/**
 * @template E
 * @template A
 * @extends Semigroup<Validated<E, A>>
 */
class ValidatedSemigroup extends Semigroup
{
    /**
     * @param Semigroup<A> $validSemigroup
     * @param Semigroup<E> $invalidSemigroup
     */
    public function __construct(
        private Semigroup $validSemigroup,
        private Semigroup $invalidSemigroup
    ) {}

    /**
     * @param Validated<E, A> $lhs
     * @param Validated<E, A> $rhs
     * @return Validated<E, A>
     */
    public function combine(mixed $lhs, mixed $rhs): Validated
    {
        return match (true) {
            $lhs->isValid() && $rhs->isValid() => Validated::valid(
                $this->validSemigroup->combine($lhs->get(), $rhs->get())
            ),
            $lhs->isInvalid() && $rhs->isInvalid() => Validated::invalid(
                $this->invalidSemigroup->combine($lhs->get(), $rhs->get())
            ),
            default => $rhs->isInvalid() ? $rhs : $lhs,
        };
    }
}
