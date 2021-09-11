<?php

declare(strict_types=1);

namespace Tests\Mock;

use Fp\Collections\HashContract;

/**
 * @internal
 */
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true, public bool $c = true)
    {
    }

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}
