<?php

declare(strict_types=1);

namespace Fp\Functional\AndThen;

use Closure;

/**
 * @psalm-immutable
 * @template A
 * @template-covariant B
 * @extends AndThen<A, B>
 */
final class Single extends AndThen
{
    /**
     * @param Closure(A): B $run
     */
    public function __construct(public Closure $run, public int $index) { }

    /**
     * @psalm-pure
     * @template AA
     * @template BB
     * @param Closure(AA): BB $run
     * @return self<AA, BB>
     */
    public static function of(Closure $run, int $index): self
    {
        return new self($run, $index);
    }
}
