<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template T
 */
interface HashContract
{
    /**
     * @psalm-param T $rhs
     */
    public function equals(mixed $rhs): bool;

    public function hashCode(): string;
}
