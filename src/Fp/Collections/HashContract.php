<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * ```php
 * class Foo implements HashContract
 * {
 *     public function __construct(public int $a, public bool $b = true, public bool $c = true)
 *     {
 *     }
 *
 *     public function equals(mixed $that): bool
 *     {
 *         return $that instanceof self
 *             && $this->a === $that->a
 *             && $this->b === $that->b;
 *     }
 *
 *     public function hashCode(): string
 *     {
 *         return md5(implode(',', [$this->a, $this->b]));
 *     }
 * }
 * ```
 */
interface HashContract
{
    /**
     * Compare $this and $that.
     * Must return true if $this equals $that.
     */
    public function equals(mixed $that): bool;

    /**
     * Equal objects MUST return equal hash code.
     * But hash code equality is not 100% guarantee that comparison subjects are equal.
     * Hash code equality tells that comparison subjects are probably equals (with high percentage).
     */
    public function hashCode(): string;
}
