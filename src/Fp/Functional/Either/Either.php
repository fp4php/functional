<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

use Fp\Functional\Option\Option;
use Fp\Functional\Validated\Validated;
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
     * Unwrap "the box" and get contained success value
     * or given fallback for case when
     * there is error value in the box.
     *
     * REPL:
     * >>> Either::right(1)->getOrElse(0)
     * => 1
     * >>> Either::left('error')->getOrElse(0)
     * => 0
     *
     * @psalm-template F
     * @psalm-param F $fallback
     * @psalm-return R|F
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->isRight()
            ? $this->value
            : $fallback;
    }

    /**
     * Unwrap "the box" and get contained success value
     * or given fallback call result for case when
     * there is error value in the box.
     *
     * REPL:
     * >>> Either::right(1)->getOrCall(fn() => 0)
     * => 1
     * >>> Either::left('error')->getOrCall(fn() => 0)
     * => 0
     *
     * @psalm-template F
     * @psalm-param callable(): F $fallback
     * @psalm-return R|F
     */
    public function getOrCall(callable $fallback): mixed
    {
        return $this->isRight()
            ? $this->value
            : $fallback();
    }

    /**
     * Combine two Either into one
     *
     * REPL:
     * >>> Either::right(1)->orElse(fn() => Either::right(2))
     * => Right(1)
     * >>> Either::left(1)->orElse(fn() => Either::right(2))
     * => Right(2)
     *
     * @psalm-template LL
     * @psalm-template RR
     * @psalm-param callable(): Either<LL, RR> $fallback
     * @psalm-return Either<L|LL, R|RR>
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
     * REPL:
     * >>> Either::right(1)->fold(
     *     ifRight: fn(int $right) => $right + 1,
     *     ifLeft: fn(string $left) => $left . '!',
     * );
     * => 2
     * >>> Either::left('error')->fold(
     *     ifRight: fn(int $right) => $right + 1,
     *     ifLeft: fn(string $left) => $left . '!',
     * );
     * => 'error!'
     *
     * @psalm-template TO
     * @psalm-param callable(R): TO $ifRight
     * @psalm-param callable(L): TO $ifLeft
     * @psalm-return TO
     */
    public function fold(callable $ifRight, callable $ifLeft): mixed
    {
        return $this->isRight()
            ? $ifRight($this->value)
            : $ifLeft($this->value);
    }

    /**
     * 1) Unwrap the box
     * 2) If the box contains error value then do nothing
     * 3) Pass unwrapped success value to callback
     * 4) Place callback result value into the new success-box
     *
     * REPL:
     * >>> $res1 = Either::right(1);
     * => Right(1)
     * >>> $res2 = $res1->map(fn(int $i) => $i + 1);
     * => Right(2)
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Right('2')
     *
     * @psalm-template RO
     * @psalm-param callable(R): RO $callback
     * @psalm-return Either<L, RO>
     */
    public function map(callable $callback): Either
    {
        return $this->isLeft()
            ? new Left($this->value)
            : new Right($callback($this->value));
    }

    /**
     * 1) Unwrap the box
     * 2) If the box contains success value then do nothing
     * 3) Pass unwrapped error value to callback
     * 4) Place callback result value into the new error-box
     *
     * REPL:
     * >>> $res1 = Either::left(0);
     * => Left(0)
     * >>> $res2 = $res1->mapLeft(fn(int $i) => match ($i) {
     *     0 => 'error',
     *     default => 'warning'
     * });
     * => Left('error')
     *
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
     * 1) Unwrap the box
     * 2) If the box contains error value then do nothing
     * 3) Pass unwrapped success value to callback
     * 4) Replace old box with new one returned by callback
     *
     * REPL:
     * >>> $res1 = Either::right(1);
     * => Right(1)
     * >>> $res2 = $res1->flatMap(fn(int $i) => Either::right($i + 1));
     * => Right(2)
     * >>> $res3 = $res2->flatMap(fn(int $i) => Either::left('error'));
     * => Left('error')
     *
     * @psalm-template RO
     * @psalm-param callable(R): Either<L, RO> $callback
     * @psalm-return Either<L, RO>
     */
    public function flatMap(callable $callback): Either
    {
        return $this->isLeft()
            ? new Left($this->value)
            : $callback($this->value);
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
     * REPL:
     * >>> Either::do(function() {
     *     $a = 1;
     *     $b = yield Either::right(2);
     *     $c = yield new Right(3);
     *     $d = yield Either::left('error!'); // short circuit here
     *     $e = 5;                            // not executed
     *     return [$a, $b, $c, $d, $e];       // not executed
     * });
     * => Left('error!')
     *
     * @todo Replace Either<TL, mixed> with Either<TL, TR> and drop suppress @see https://github.com/vimeo/psalm/issues/6288
     *
     * @template TL
     * @template TR
     * @template TO
     * @psalm-param callable(): Generator<int, Either<TL, mixed>, TR, TO> $computation
     * @psalm-return Either<TL, TO>
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
     * REPL:
     * >>> Either::try(fn() => 1);
     * => Right(1)
     * >>> Either::try(fn() => throw new Exception('handled and converted to Left'));
     * => Left(Exception('handled and converted to Left'))
     *
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
     * Convert Either to Option
     *
     * REPL:
     * >>> Either::right(1)->toOption()
     * => Some(1)
     * >>> Either::left('error')->toOption()
     * => None
     *
     * @psalm-return Option<R>
     */
    public function toOption(): Option
    {
        return $this->isRight()
            ? Option::some($this->value)
            : Option::none();
    }

    /**
     * Convert Either to Validated
     *
     * REPL:
     * >>> Either::right(1)->toValidated()
     * => Valid(1)
     * >>> Either::left('error')->toValidated()
     * => Invalid('error')
     *
     * @psalm-return Validated<L, R>
     */
    public function toValidated(): Validated
    {
        return $this->isRight()
            ? Validated::valid($this->value)
            : Validated::invalid($this->value);
    }

    /**
     * Check if there is error value contained in the box
     *
     * REPL:
     * >>> Either::some(1)->isLeft()
     * => false
     * >>> Either::left('error')->isLeft()
     * => true
     *
     * @psalm-assert-if-true Left<L> $this
     */
    public function isLeft(): bool
    {
        return $this instanceof Left;
    }

    /**
     * Check if there is success value contained in the box
     *
     * REPL:
     * >>> Either::some(1)->isRight()
     * => true
     * >>> Either::left('error')->isRight()
     * => false
     *
     * @psalm-assert-if-true Right<R> $this
     */
    public function isRight(): bool
    {
        return $this instanceof Right;
    }

    /**
     * Swap error outcome with success outcome (Left with Right)
     *
     * REPL:
     * >>> Either::some(1)->swap()
     * => Left(1)
     * >>> Either::left(1)->swap()
     * => Right(1)
     *
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
     * Fabric method.
     *
     * Create {@see Left} from value
     *
     * REPL:
     * >>> Either::left('error')
     * => Left('error')
     *
     * @psalm-template LI
     * @psalm-param LI $value
     * @psalm-return Either<LI, empty>
     * @psalm-pure
     */
    public static function left(mixed $value): Either
    {
        return Left::of($value);
    }

    /**
     * Fabric method.
     *
     * Create {@see Right} from value
     *
     * REPL:
     * >>> Either::right(1)
     * => Right(1)
     *
     * @psalm-template RI
     * @psalm-param RI $value
     * @psalm-return Either<empty, RI>
     * @psalm-pure
     */
    public static function right(mixed $value): Either
    {
        return Right::of($value);
    }

    /**
     * Fabric method.
     *
     * REPL:
     * >>> Either::cond(true, 1, 'error')
     * => Right(1)
     * >>> Either::cond(false, 1, 'error')
     * => Left('error')
     *
     * Create {@see Right} from value if given condition is true
     * Create {@see Left} from value if given condition is false
     *
     * @psalm-template LI
     * @psalm-template RI
     * @psalm-param LI $left
     * @psalm-param RI $right
     * @psalm-return Either<LI, RI>
     * @psalm-pure
     */
    public static function cond(bool $condition, mixed $right, mixed $left): Either
    {
        return $condition
            ? Right::of($right)
            : Left::of($left);
    }

    /**
     * Unwrap the box value
     *
     * REPL:
     * >>> Either::some(1)->get()
     * => 1
     *
     * @psalm-return L|R
     */
    abstract public function get(): mixed;
}
