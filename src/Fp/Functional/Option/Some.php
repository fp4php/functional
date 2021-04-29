<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @template-covariant A
 * @psalm-immutable
 * @extends Option<A>
 */
final class Some extends Option
{
    /**
     * @param A $value
     */
    public function __construct(int|float|bool|string|object $value)
    {
        parent::__construct($value);
    }

    /**
     * @psalm-return A
     */
    public function get(): mixed
    {
        /** @psalm-var A $value */
        $value = $this->value;

        return $value;
    }
}
