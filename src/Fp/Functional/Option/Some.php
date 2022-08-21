<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @template-covariant A
 * @extends Option<A>
 */
final class Some extends Option
{
    /**
     * @param A $value
     */
    public function __construct(
        private readonly mixed $value,
    ) {}

    /**
     * @return A
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
