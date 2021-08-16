<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV of (object|scalar)
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

    /**
     * Produces new set with given element included
     *
     * @template TVI of (object|scalar)
     * @param TVI $element
     * @return Set<TV|TVI>
     */
    public function updated(mixed $element): Set;

    /**
     * Produces new set with given element excluded
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;
}
