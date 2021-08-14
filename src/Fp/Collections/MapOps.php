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

    /**
     * @template TKI of (object|scalar)
     * @template TVI
     * @param TKI $key
     * @param TVI $value
     * @return Map<TK|TKI, TV|TVI>
     */
    public function put(mixed $key, mixed $value): Map;
}
