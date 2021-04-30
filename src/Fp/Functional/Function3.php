<?php

declare(strict_types=1);

namespace Fp\Functional;

use Closure;

/**
 * @template TI1
 * @template TI2
 * @template TI3
 * @template TO
 * @psalm-immutable
 */
class Function3 implements FunctionInterface
{
    /**
     * @var Closure(TI1, TI2, TI3): TO
     */
    private Closure $callback;

    /**
     * @param Closure(TI1, TI2, TI3): TO $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @psalm-param TI1 $i1
     * @psalm-param TI2 $i2
     * @psalm-return TO
     */
    public function __invoke(mixed $i1, mixed $i2, mixed $i3): mixed
    {
        return ($this->callback)($i1, $i2, $i3);
    }
}
