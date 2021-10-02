<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

/**
 * @internal
 * @experimental
 * @psalm-immutable
 * @template A
 * @extends Trampoline<A>
 */
final class Done extends Trampoline
{
    /**
     * @param A $value
     */
    public function __construct(public mixed $value) { }

    /**
     * @psalm-pure
     * @template AA
     * @param AA $value
     * @return self<AA>
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }
}
