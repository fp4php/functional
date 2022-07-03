<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

use Fp\Functional\Option\Option;
use Fp\Functional\Validated\Validated;
use Fp\Operations\ToStringOperation;
use Generator;
use Throwable;

/**
 * @template-covariant L
 * @template-covariant R
 * @psalm-yield R
 *
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Either
{
    /**
     * Unwrap "the box" and get contained success value
     * or given fallback for case when
     * there is error value in the box.
     *
     * ```php
     * >>> Either::right(1)->getOrElse(0);
     * => 1
     *
     * >>> Either::left('error')->getOrElse(0);
     * => 0
     * ```
     *
     * @template F
     *
     * @param F $fallback
     * @return R|F
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->isRight()
            ? $this->get()
            : $fallback;
    }

    /**
     * Unwrap "the box" and get contained success value
     * or given fallback call result for case when
     * there is error value in the box.
     *
     * ```php
     * >>> Either::right(1)->getOrCall(fn() => 0);
     * => 1
     *
     * >>> Either::left('error')->getOrCall(fn() => 0);
     * => 0
     * ```
     *
     * @template F
     *
     * @param callable(): F $fallback
     * @return R|F
     */
    public function getOrCall(callable $fallback): mixed
    {
        return $this->isRight()
            ? $this->get()
            : $fallback();
    }

    /**
     * ```php
     * >>> Either::right(1)->getOrThrow(fn($err) => new RuntimeException($err));
     * => 1
     *
     * >>> Either::left('error')->getOrThrow(fn($err) => new RuntimeException($err));
     * RuntimeException with message 'error'
     * ```
     *
     * @param callable(L): Throwable $fallback
     * @return R
     */
    public function getOrThrow(callable $fallback): mixed
    {
        return $this->isRight()
            ? $this->get()
            : throw $fallback($this->get());
    }

    /**
     * Combine two Either into one
     *
     * ```php
     * >>> Either::right(1)->orElse(fn() => Either::right(2));
     * => Right(1)
     *
     * >>> Either::left(1)->orElse(fn() => Either::right(2));
     * => Right(2)
     * ```
     *
     * @template LL
     * @template RR
     *
     * @param callable(): Either<LL, RR> $fallback
     * @return Either<L|LL, R|RR>
     */
    public function orElse(callable $fallback): Either
    {
        return $this->isRight()
            ? $this
            : $fallback();
    }

    /**
     * Fold possible outcomes
     *
     * ```php
     * >>> Either::right(1)->fold(
     *     ifRight: fn(int $right) => $right + 1,
     *     ifLeft: fn(string $left) => $left . '!',
     * );
     * => 2
     *
     * >>> Either::left('error')->fold(
     *     ifRight: fn(int $right) => $right + 1,
     *     ifLeft: fn(string $left) => $left . '!',
     * );
     * => 'error!'
     * ```
     *
     * @template TOutLeft
     * @template TOutRight
     *
     * @param callable(R): TOutRight $ifRight
     * @param callable(L): TOutLeft $ifLeft
     * @return TOutRight|TOutLeft
     */
    public function fold(callable $ifRight, callable $ifLeft): mixed
    {
        return $this->isRight()
            ? $ifRight($this->get())
            : $ifLeft($this->get());
    }

    /**
     * 1) Unwrap the box
     * 2) If the box contains error value then do nothing
     * 3) Pass unwrapped success value to callback
     * 4) Place callback result value into the new success-box
     *
     * ```php
     * >>> $res1 = Either::right(1);
     * => Right(1)
     *
     * >>> $res2 = $res1->map(fn(int $i) => $i + 1);
     * => Right(2)
     *
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Right('2')
     * ```
     *
     * @template RO
     *
     * @param callable(R): RO $callback
     * @return Either<L, RO>
     */
    public function map(callable $callback): Either
    {
        return $this->isLeft()
            ? new Left($this->get())
            : new Right($callback($this->get()));
    }

    /**
     * 1) Unwrap the box
     * 2) If the box contains success value then do nothing
     * 3) Pass unwrapped error value to callback
     * 4) Place callback result value into the new error-box
     *
     * ```php
     * >>> $res1 = Either::left(0);
     * => Left(0)
     *
     * >>> $res2 = $res1->mapLeft(fn(int $i) => match ($i) {
     *     0 => 'error',
     *     default => 'warning'
     * });
     * => Left('error')
     * ```
     *
     * @template LO
     *
     * @param callable(L): LO $callback
     * @return Either<LO, R>
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
     * 1) Unwrap the box
     * 2) If the box contains error value then do nothing
     * 3) Pass unwrapped success value to callback
     * 4) Replace old box with new one returned by callback
     *
     * ```php
     * >>> $res1 = Either::right(1);
     * => Right(1)
     *
     * >>> $res2 = $res1->flatMap(fn(int $i) => Either::right($i + 1));
     * => Right(2)
     *
     * >>> $res3 = $res2->flatMap(fn(int $i) => Either::left('error'));
     * => Left('error')
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param callable(R): Either<LO, RO> $callback
     * @return Either<LO|L, RO>
     */
    public function flatMap(callable $callback): Either
    {
        return $this->isLeft()
            ? new Left($this->get())
            : $callback($this->get());
    }

    /**
     * Do-notation a.k.a. for-comprehension.
     *
     * Syntax sugar for sequential {@see Either::flatMap()} calls
     *
     * Syntax "$unwrappedValue = yield $box" mean:
     * 1) unwrap the $box
     * 2) if there is error in the box then short-circuit (stop) the computation
     * 3) place contained in $box value into $unwrappedValue variable
     *
     * ```php
     * >>> Either::do(function() {
     *     $a = 1;
     *     $b = yield Either::right(2);
     *     $c = yield new Right(3);
     *     $d = yield Either::left('error!'); // short circuit here
     *     $e = 5;                            // not executed
     *     return [$a, $b, $c, $d, $e];       // not executed
     * });
     * => Left('error!')
     * ```
     *
     * @todo Replace Either<TL, mixed> with Either<TL, TR> and drop suppress @see https://github.com/vimeo/psalm/issues/6288
     *
     * @template TL
     * @template TR
     * @template TO
     *
     * @param callable(): Generator<int, Either<TL, mixed>, TR, TO> $computation
     * @return Either<TL, TO>
     */
    public static function do(callable $computation): Either {
        $generator = $computation();

        while ($generator->valid()) {
            $currentStep = $generator->current();

            if ($currentStep->isRight()) {
                /** @psalm-suppress MixedArgument */
                $generator->send($currentStep->get());
            } else {
                /** @var Either<TL, TO> $currentStep */
                return $currentStep;
            }

        }

        return new Right($generator->getReturn());
    }

    /**
     * Fabric method which creates Either.
     *
     * Try/catch replacement.
     *
     * ```php
     * >>> Either::try(fn() => 1);
     * => Right(1)
     *
     * >>> Either::try(fn() => throw new Exception('handled and converted to Left'));
     * => Left(Exception('handled and converted to Left'))
     * ```
     *
     * @template TRI
     *
     * @param callable(): TRI $callback
     * @return Either<Throwable, TRI>
     */
    public static function try(callable $callback): Either
    {
        try {
            return Either::right($callback());
        } catch (Throwable $exception) {
            return Either::left($exception);
        }
    }

    /**
     * Convert Either to Option
     *
     * ```php
     * >>> Either::right(1)->toOption();
     * => Some(1)
     *
     * >>> Either::left('error')->toOption();
     * => None
     * ```
     *
     * @return Option<R>
     */
    public function toOption(): Option
    {
        return $this->isRight()
            ? Option::some($this->get())
            : Option::none();
    }

    /**
     * Convert Either to Validated
     *
     * ```php
     * >>> Either::right(1)->toValidated();
     * => Valid(1)
     *
     * >>> Either::left('error')->toValidated();
     * => Invalid('error')
     * ```
     *
     * @return Validated<L, R>
     */
    public function toValidated(): Validated
    {
        return $this->isRight()
            ? Validated::valid($this->get())
            : Validated::invalid($this->get());
    }

    /**
     * Check if there is error value contained in the box
     *
     * ```php
     * >>> Either::some(1)->isLeft();
     * => false
     *
     * >>> Either::left('error')->isLeft();
     * => true
     * ```
     *
     * @psalm-assert-if-true Left<L>&\Fp\Functional\Assertion<"must-be-left"> $this
     */
    public function isLeft(): bool
    {
        return $this instanceof Left;
    }

    /**
     * Check if there is success value contained in the box
     *
     * ```php
     * >>> Either::some(1)->isRight();
     * => true
     *
     * >>> Either::left('error')->isRight();
     * => false
     * ```
     *
     * @psalm-assert-if-true Right<R>&\Fp\Functional\Assertion<"must-be-right"> $this
     */
    public function isRight(): bool
    {
        return $this instanceof Right;
    }

    /**
     * Swap error outcome with success outcome (Left with Right)
     *
     * ```php
     * >>> Either::some(1)->swap();
     * => Left(1)
     *
     * >>> Either::left(1)->swap();
     * => Right(1)
     * ```
     *
     * @return Either<R, L>
     */
    public function swap(): Either
    {
        return $this->isLeft()
            ? Either::right($this->get())
            : Either::left($this->get());
    }

    /**
     * Fabric method.
     *
     * Create {@see Left} from value
     *
     * ```php
     * >>> Either::left('error');
     * => Left('error')
     * ```
     *
     * @template LI
     *
     * @param LI $value
     * @return Either<LI, empty>
     */
    public static function left(mixed $value): Either
    {
        return new Left($value);
    }

    /**
     * Fabric method.
     *
     * Create {@see Right} from value
     *
     * ```php
     * >>> Either::right(1);
     * => Right(1)
     * ```
     *
     * @template RI
     *
     * @param RI $value
     * @return Either<empty, RI>
     */
    public static function right(mixed $value): Either
    {
        return new Right($value);
    }

    /**
     * Fabric method.
     *
     * ```php
     * >>> Either::cond(true, 1, 'error');
     * => Right(1)
     *
     * >>> Either::cond(false, 1, 'error');
     * => Left('error')
     * ```
     *
     * @template LI
     * @template RI
     *
     * @param LI $left
     * @param RI $right
     * @return Either<LI, RI>
     */
    public static function cond(bool $condition, mixed $right, mixed $left): Either
    {
        return $condition
            ? Either::right($right)
            : Either::left($left);
    }

    /**
     * Fabric method.
     *
     * ```php
     * >>> Either::condLazy(true, fn() => 1, fn() => 'error');
     * => Right(1)
     *
     * >>> Either::condLazy(false, fn() => 1, fn() => 'error');
     * => Left('error')
     * ```
     *
     * Create {@see Right} from callable return value if given condition is true
     * Create {@see Left} from callable return value if given condition is false
     *
     * @template LI
     * @template RI
     *
     * @param callable(): LI $left
     * @param callable(): RI $right
     * @return Either<LI, RI>
     */
    public static function condLazy(bool $condition, callable $right, callable $left): Either
    {
        return $condition
            ? Either::right($right())
            : Either::left($left());
    }

    /**
     * Unwrap the box value
     *
     * ```php
     * >>> Either::some(1)->get();
     * => 1
     * ```
     *
     * @return L|R
     */
    abstract public function get(): mixed;

    public function __toString(): string
    {
        $value = ToStringOperation::of($this->get());

        return $this instanceof Left
            ? "Left({$value})"
            : "Right({$value})";
    }
}
