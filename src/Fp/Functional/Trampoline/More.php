<?php

declare(strict_types=1);

namespace Fp\Functional\Trampoline;

use Closure;

/**
 * @internal
 * @experimental
 * @psalm-immutable
 * @template A
 * @extends Trampoline<A>
 */
final class More extends Trampoline
{
    /**
     * @param Closure(): Trampoline<A> $resume
     */
    public function __construct(public Closure $resume) { }

    /**
     * @psalm-pure
     * @template AA
     * @param Closure(): Trampoline<AA> $resume
     * @return self<AA>
     */
    public static function of(Closure $resume): self
    {
        return new self($resume);
    }
}
