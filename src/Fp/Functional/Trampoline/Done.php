<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

/**
 * @template A
 * @extends Trampoline<A>
 */
final class Done extends Trampoline
{
    /**
     * @param A $value
     */
    public function __construct(public mixed $value) { }
}
