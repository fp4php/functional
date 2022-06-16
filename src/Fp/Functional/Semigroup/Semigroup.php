<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template A
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Semigroup
{
    /**
     * @psalm-param A $lhs
     * @psalm-param A $rhs
     * @psalm-return A
     */
    abstract public function combine(mixed $lhs, mixed $rhs): mixed;

    /**
     * @psalm-param non-empty-list<A> $elements
     * @psalm-return A
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
