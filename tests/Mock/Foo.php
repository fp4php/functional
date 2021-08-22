<?php

declare(strict_types=1);

namespace Tests\Mock;

use Fp\Collections\HashContract;

/**
 * @internal
 */
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true)
    {
    }

    public function equals(mixed $rhs): bool
    {
        return $rhs instanceof self
            && $this->a === $rhs->a
            && $this->b === $rhs->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}
