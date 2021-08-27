<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
final class Cons
{
    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(public mixed $head, public LinkedList $tail)
    {
    }
}
