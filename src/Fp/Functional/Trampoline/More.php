<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @template A
 * @extends Trampoline<A>
 */
final class More extends Trampoline
{
    /**
     * @param Closure(): Trampoline<A> $resume
     */
    public function __construct(public Closure $resume) { }
}
