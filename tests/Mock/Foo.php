<?php

declare(strict_types=1);

namespace Tests\Mock;

use Fp\Collections\HashContract;

/**
 * @internal
 * @implements HashContract<Foo>
 */
class Foo implements HashContract
{
    public function __construct(public int $a)
    {
    }

    public static function test(int $a, bool $b): string
    {
        return 'x' . $a;
    }

    public function equals(mixed $rhs): bool
    {
        return $this->a === $rhs->a;
    }

    public function hashCode(): string
    {
        return implode(',', [md5((string) $this->a)]);
    }
}
