<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant L
 * @template-covariant R
 * @psalm-immutable
 */
abstract class Either
{
    /**
     * @psalm-param Closure(): R $or
     * @psalm-return R
     */
    public function getOrElse(\Closure $or): mixed
    {
        return $this->isRight() ? $this->get() : $or();
    }

    /**
     * @template T
     * @psalm-param \Closure(L): T $ifLeft
     * @psalm-param \Closure(R): T $ifRight
     * @return T
     */
    public function fold(\Closure $ifLeft, \Closure $ifRight): mixed
    {
        if ($this->isRight()) {
            return $ifRight($this->get());
        } else {
            /**
             * @var Left<L, R> $this
             */

            return $ifLeft($this->get());
        }
    }

    /**
     * @psalm-assert-if-true Left<L, R> $this
     */
    public function isLeft(): bool
    {
        return $this instanceof Left;
    }

    /**
     * @psalm-assert-if-true Right<L, R> $this
     */
    public function isRight(): bool
    {
        return $this instanceof Right;
    }
}
