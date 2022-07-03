<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template A
 */
abstract class Semigroup
{
    /**
     * @param A $lhs
     * @param A $rhs
     * @return A
     */
    abstract public function combine(mixed $lhs, mixed $rhs): mixed;

    /**
     * @param non-empty-list<A> $elements
     * @return A
     */
    public function combineAll(array $elements): mixed
    {
        $acc = array_shift($elements);

        foreach ($elements as $element) {
            $acc = $this->combine($acc, $element);
        }

        return $acc;
    }
}
