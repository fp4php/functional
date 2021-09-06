<?php

declare(strict_types=1);

namespace Fp\Collections;

use Closure;
use Error;

/**
 * Internal conditionally pure thunk
 *
 * @internal
 * @template T
 * @psalm-immutable
 */
final class PureThunk
{
    /**
     * @psalm-allow-private-mutation $evaluated
     * @var bool
     */
    private bool $evaluated = false;

    /**
     * @psalm-param Closure(): T $thunk
     */
    public function __construct(private Closure $thunk)
    {
    }

    /**
     * @psalm-pure
     * @template TI
     * @param Closure(): TI $thunk
     * @return self<TI>
     */
    public static function of(Closure $thunk): self
    {
       return new self($thunk);
    }

    /**
     * @return T
     */
    public function __invoke(): mixed
    {
        if ($this->evaluated) {
            throw new Error(self::class . ' is not pure');
        } else {
            $this->evaluated = true;
        }

        return ($this->thunk)();
    }
}
