<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK of (object|scalar)
 * @template-covariant TV
 * @psalm-immutable
 */
interface MapOps
{
    /**
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option;
}
