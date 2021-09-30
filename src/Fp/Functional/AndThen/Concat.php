<?php

declare(strict_types=1);

namespace Fp\Functional\AndThen;

/**
 * @psalm-immutable
 * @template A
 * @template E
 * @template-covariant B
 * @extends AndThen<A, B>
 */
final class Concat extends AndThen
{
    /**
     * @param AndThen<A, E> $left
     * @param AndThen<E, B> $right
     */
    public function __construct(public AndThen $left, public AndThen $right) { }

    /**
     * @psalm-pure
     * @template AA
     * @template EE
     * @template BB
     * @param AndThen<AA, EE> $left
     * @param AndThen<EE, BB> $right
     * @return self<AA, EE, BB>
     */
    public static function of(AndThen $left, AndThen $right): self
    {
        return new self($left, $right);
    }
}
