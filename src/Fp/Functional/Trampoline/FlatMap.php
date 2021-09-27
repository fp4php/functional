<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @psalm-immutable
 * @template A
 * @template B
 * @extends Trampoline<B>
 */
final class FlatMap extends Trampoline
{
    /**
     * @param Trampoline<A> $sub
     * @param Closure(A): Trampoline<B> $cont
     */
    public function __construct(public Trampoline $sub, public Closure $cont) { }

    /**
     * @psalm-pure
     * @template AA
     * @template BB
     * @param Trampoline<AA> $sub
     * @param Closure(AA): Trampoline<BB> $cont
     * @return self<AA, BB>
     */
    public static function of(Trampoline $sub, Closure $cont): self
    {
        return new self($sub, $cont);
    }
}
