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
     * @param Closure(A): B $func
     */
    public function __construct(public Closure $func, public int $index) { }

    /**
     * @psalm-pure
     * @template AA
     * @template BB
     * @param Closure(AA): BB $fun
     * @return self<AA, BB>
     */
    public static function of(Closure $fun, int $index): self
    {
        return new self($fun, $index);
    }
}
