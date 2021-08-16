<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SetOps
{
    /**
     * Check if the element is present in the set
     * Alias for @see SetOps::contains
     *
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool;
}
