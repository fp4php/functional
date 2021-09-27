<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @template A
 * @template B
 * @extends Trampoline<B>
 */
final class FlatMap extends Trampoline
{
    /**
     * @param Trampoline<A> $subject
     * @param Closure(A): Trampoline<B> $kleisli
     */
    public function __construct(public Trampoline $subject, public Closure $kleisli) { }
}
