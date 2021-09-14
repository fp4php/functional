<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;

/**
 * @template E
 * @template A
 * @psalm-immutable
 * @extends Semigroup<Validated<E, A>>
 */
class ValidatedSemigroup extends Semigroup
{
    /**
     * @psalm-param Semigroup<A> $validSemigroup
     * @psalm-param Semigroup<E> $invalidSemigroup
     */
    public function __construct(
        private Semigroup $validSemigroup,
        private Semigroup $invalidSemigroup
    )
    {
    }

    /**
     * @psalm-param Validated<E, A> $lhs
     * @psalm-param Validated<E, A> $rhs
     * @psalm-return Validated<E, A>
     */
    public function combine(mixed $lhs, mixed $rhs): Validated
    {
        if ($lhs->isValid() && $rhs->isValid()) {
            /**
             * @var Valid<A> $lhs
             */
            return Validated::valid($this->validSemigroup->combine(
                $lhs->get(),
                $rhs->get()
            ));
        }

        if ($lhs->isInvalid() && $rhs->isInvalid()) {
            /**
             * @var Invalid<E> $lhs
             * @var Invalid<E> $rhs
             */
            return Validated::invalid($this->invalidSemigroup->combine(
                $lhs->get(),
                $rhs->get()
            ));
        }

        return $rhs->isInvalid() ? $rhs : $lhs;
    }
}
