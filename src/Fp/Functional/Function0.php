<?php

declare(strict_types=1);

namespace Fp\Functional;

use Closure;

/**
 * @template TO
 * @psalm-immutable
 */
class Function0
{
    /**
     * @var Closure(): TO
     */
    private Closure $callback;

    /**
     * @param Closure(): TO $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @psalm-return TO
     */
    public function __invoke(): mixed
    {
        return ($this->callback)();
    }
}
