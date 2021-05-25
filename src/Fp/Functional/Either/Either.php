<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

use Fp\Functional\Option\Option;
use Generator;
use Throwable;

/**
 * @template-covariant L
 * @template-covariant R
 * @psalm-yield R
 * @psalm-immutable
 */
abstract class Either
{
    /**
     * @psalm-template F
     * @psalm-param F $fallback
     * @psalm-return R|F
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->isRight() ? $this->value : $fallback;
    }

    /**
     * @psalm-template TO
     * @psalm-param callable(R): TO $ifRight
     * @psalm-param callable(L): TO $ifLeft
     * @psalm-return TO
     */
    public function fold(callable $ifRight, callable $ifLeft): mixed
    {
        if ($this->isRight()) {
            return call_user_func($ifRight, $this->value);
        } else {
            /**
             * @var Left<L, R> $this
             */

            return call_user_func($ifLeft, $this->value);
        }
    }

    /**
     * @psalm-template RO
     * @psalm-param callable(R): RO $callback
     * @psalm-return Either<L, RO>
     */
    public function map(callable $callback): Either
    {
        if ($this->isLeft()) {
            return new Left($this->value);
        }

        /**
         * @var Right<R> $this
         */

        return new Right(call_user_func($callback, $this->value));
    }

    /**
     * @psalm-template LO
     * @psalm-param callable(L): LO $callback
     * @psalm-return Either<LO, R>
     */
    public function mapLeft(callable $callback): Either
    {
        /** @psalm-suppress InvalidArgument */
        return $this
            ->swap()
            ->map($callback)
            ->swap();
    }

    /**
     * @psalm-template RO
     * @psalm-param callable(R): Either<L, RO> $callback
     * @psalm-return Either<L, RO>
     */
    public function flatMap(callable $callback): Either
    {
        if ($this->isLeft()) {
            return new Left($this->value);
        }

        /**
         * @var Right<R> $this
         */

        return call_user_func($callback, $this->value);
    }

    /**
     * @template TL
     * @template TR
     * @template TO
     * @psalm-param callable(): Generator<int, Either<TL, TR>, TR, TO> $computation
     * @psalm-return Either<TL, TO>
     */
    public static function do(callable $computation): Either {
        $generator = $computation();

        while ($generator->valid()) {
            $currentStep = $generator->current();

            if ($currentStep->isRight()) {
                $generator->send($currentStep->get());
            } else {
                /** @var Either<TL, TO> $currentStep */
                return $currentStep;
            }

        }

        return new Right($generator->getReturn());
    }

    /**
     * @psalm-template TLI of Throwable
     * @psalm-template TRI
     * @psalm-param callable(): TRI $callback
     * @psalm-return Either<TLI, TRI>
     */
    public static function try(callable $callback): Either
    {
        try {
            return Right::of(call_user_func($callback));
        } catch (Throwable $exception) {
            /** @var Left<TLI> */
            return Left::of($exception);
        }
    }

    /**
     * @psalm-return Option<R>
     */
    public function toOption(): Option
    {
        return $this->isRight() ? Option::some($this->value) : Option::none();
    }

    /**
     * @psalm-assert-if-true Left<L> $this
     */
    public function isLeft(): bool
    {
        return $this instanceof Left;
    }

    /**
     * @psalm-assert-if-true Right<R> $this
     */
    public function isRight(): bool
    {
        return $this instanceof Right;
    }

    /**
     * @psalm-return Either<R, L>
     */
    public function swap(): Either
    {
        return match (true) {
            ($this instanceof Right) => new Left($this->value),
            ($this instanceof Left) => new Right($this->value),
        };
    }

    /**
     * @psalm-template LI
     * @psalm-param LI $value
     * @psalm-return Either<LI, empty>
     * @psalm-pure
     */
    public static function left(int|float|bool|string|object|array $value): Either
    {
        return Left::of($value);
    }

    /**
     * @psalm-template RI
     * @psalm-param RI $value
     * @psalm-return Either<empty, RI>
     * @psalm-pure
     */
    public static function right(int|float|bool|string|object|array $value): Either
    {
        return Right::of($value);
    }

    /**
     * @psalm-template LI
     * @psalm-template RI
     * @psalm-param LI $left
     * @psalm-param RI $right
     * @psalm-return Either<LI, RI>
     * @psalm-pure
     */
    public static function cond(
        bool $condition,
        int|float|bool|string|object|array $right,
        int|float|bool|string|object|array $left,
    ): Either
    {
        return $condition
            ? Right::of($right)
            : Left::of($left);
    }

    /**
     * @psalm-return L|R
     */
    abstract public function get(): int|float|bool|string|object|array;
}
