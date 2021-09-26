<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

/**
 * @template A
 * @extends TailRec<A>
 */
final class Returned extends TailRec
{
    /**
     * @param A $value
     */
    public function __construct(public mixed $value) { }
}
