<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @template A
 * @template B
 * @extends TailRec<B>
 */
final class FlatMap extends TailRec
{
    /**
     * @param TailRec<A> $subject
     * @param Closure(A): TailRec<B> $kleisli
     */
    public function __construct(public TailRec $subject, public Closure $kleisli) { }
}
