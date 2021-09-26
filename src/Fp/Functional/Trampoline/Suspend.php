<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @template A
 * @extends TailRec<A>
 */
final class Suspend extends TailRec
{
    /**
     * @param Closure(): TailRec<A> $resume
     */
    public function __construct(public Closure $resume) { }
}
