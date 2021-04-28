<?php

declare(strict_types=1);

namespace Fp;

use Closure;

/**
 * @template TI1
 * @template TO
 * @psalm-immutable
 */
class Function1
{
    /**
     * @var Closure(TI1): TO
     */
    private Closure $callback;

    /**
     * @param Closure(TI1): TO $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @psalm-param TI1 $i1
     * @psalm-return TO
     */
    public function __invoke(mixed $i1): mixed
    {
        return ($this->callback)($i1);
    }
}
