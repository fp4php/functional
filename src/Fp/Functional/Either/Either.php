<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

use Closure;
use Throwable;
use Generator;
use Fp\Collections\ArrayList;
use Fp\Functional\Option\Option;
use Fp\Operations\ToStringOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\EitherGetReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;

use function Fp\Callable\toSafeClosure;

/**
 * @template-covariant L
 * @template-covariant R
 * @psalm-yield R
 *
 * @psalm-seal-methods
 * @mixin EitherExtensions<L, R>
 *
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Either
{
    #region Constructors

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
     * @template LO
     *
     * @param LO $value
     * @return Either<LO, empty>
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
     * @template RO
     *
     * @param RO $value
     * @return Either<empty, RO>
     */
    public static function right(mixed $value): Either
    {
        return new Right($value);
    }

    /**
     * Fabric method.
     *
     * ```php
     * >>> Either::when(true, fn() => 1, fn() => 'err');
     * => Right(1)
     *
     * >>> Either::when(false, fn() => 1, fn() => 'err');
     * => Left('err')
     * ```
     *
     * Create {@see Right} from value if given condition is true
     * Create {@see Left} if given condition is false
     *
     * @template RO
     * @template LO
     *
     * @param callable(): RO $right
     * @return Either<LO, RO>
     */
    public static function when(bool $cond, callable $right, callable $left): Either
    {
        return $cond
            ? self::right($right())
            : self::left($left());
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
     *
     * >>> Either::try(fn() => throw new Exception('handled and converted to Left'), fn(Throwable $e) => $e->getMessage());
     * => Left('handled and converted to Left')
     *
     * >>> Either::try(fn() => throw new Exception(), fn() => 'handled and converted to Left');
     * => Left('handled and converted to Left')
     * ```
     *
     * @template RO
     * @template LO
     *
     * @param callable(): RO $callback
     * @param null|(callable(Throwable): LO) $mapLeft
     * @psalm-return ($mapLeft is null ? Either<Throwable, RO> : Either<LO, RO>)
     */
    public static function try(callable $callback, null|callable $mapLeft = null): Either
    {
        try {
            return Either::right($callback());
        } catch (Throwable $exception) {
            if (null === $mapLeft) {
                return Either::left($exception);
            }

            return Either::left($mapLeft($exception));
        }
    }

    /**
     * Traverses over $collection and return the first Right value.
     * Otherwise the last Left value.
     *
     * ```php
     * >>> Either::first([
     * >>>     Either::left('err'),
     * >>>     Either::right(42),
     * >>>     Either::right(43),
     * >>> ]);
     * => Right(42)
     *
     * >>> Either::first([
     * >>>     Either::left('err1'),
     * >>>     Option::left('err2'),
     * >>>     Option::left('err3'),
     * >>> ]);
     * => Left('err3')
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param non-empty-array<Either<LO, RO> | Closure(): Either<LO, RO>> $collection
     * @return Either<LO, RO>
     */
    public static function first(array $collection): Either
    {
        $lastLeft = null;

        foreach ($collection as $either) {
            $e = $either instanceof Closure ? $either() : $either;

            if ($e->isRight()) {
                return $e;
            } else {
                $lastLeft = $e;
            }
        }

        return $lastLeft;
    }

    /**
     * Varargs version of {@see Either::first()}.
     *
     * @template LO
     * @template RO
     *
     * @param Either<LO, RO> | Closure(): Either<LO, RO> $first
     * @param Either<LO, RO> | Closure(): Either<LO, RO> ...$tail
     * @return Either<LO, RO>
     */
    public static function firstT(Either|Closure $first, Either|Closure ...$tail): Either
    {
        return self::first([$first, ...$tail]);
    }

    /**
     * Traverses over $collection and return the first Right value.
     * Otherwise returns all left values.
     *
     * ```php
     * >>> Either::first([
     * >>>     Either::left('err'),
     * >>>     Either::right(42),
     * >>>     Either::right(43),
     * >>> ]);
     * => Right(42)
     *
     * >>> Either::first([
     * >>>     Either::left('err1'),
     * >>>     Option::left('err2'),
     * >>>     Option::left('err3'),
     * >>> ]);
     * => Left(['err1', 'err2', 'err3'])
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param non-empty-array<Either<non-empty-list<LO>, RO> | Closure(): Either<non-empty-list<LO>, RO>> $collection
     * @return Either<non-empty-list<LO>, RO>
     */
    public static function firstMerged(array $collection): Either
    {
        $merged = [];

        foreach ($collection as $either) {
            $e = $either instanceof Closure ? $either() : $either;

            if ($e->isRight()) {
                return $e;
            }

            /** @var Left<non-empty-list<LO>> $e */
            foreach ($e->get() as $left) {
                $merged[] = $left;
            }
        }

        return Either::left($merged);
    }

    /**
     * Varargs version of {@see Either::firstMerged()}.
     *
     * @template LO
     * @template RO
     *
     * @param Either<non-empty-list<LO>, RO> | Closure(): Either<non-empty-list<LO>, RO> $first
     * @param Either<non-empty-list<LO>, RO> | Closure(): Either<non-empty-list<LO>, RO> ...$tail
     * @return Either<non-empty-list<LO>, RO>
     */
    public static function firstMergedT(Either|Closure $first, Either|Closure ...$tail): Either
    {
        return self::firstMerged([$first, ...$tail]);
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
     * @template TL
     * @template TR
     * @template TO
     *
     * @param callable(): Generator<int, Either<TL, TR>, TR, TO> $computation
     * @return Either<TL, TO>
     */
    public static function do(callable $computation): Either {
        $generator = $computation();

        while ($generator->valid()) {
            $currentStep = $generator->current();

            if ($currentStep->isLeft()) {
                return $currentStep;
            }

            $generator->send($currentStep->get());
        }

        return Either::right($generator->getReturn());
    }

    #endregion Constructors

    # region Destructors

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
     * @template LO
     * @template RO
     *
     * @param callable(L): LO $ifLeft
     * @param callable(R): RO $ifRight
     * @return RO|LO
     */
    abstract public function fold(callable $ifLeft, callable $ifRight): mixed;

    /**
     * Unwrap the box value
     *
     * ```php
     * >>> Either::some(1)->get();
     * => 1
     * ```
     *
     * @return L|R
     *
     * @see EitherGetReturnTypeProvider
     */
    public function get(): mixed
    {
        return $this->fold(
            /** @param L $left */
            fn(mixed $left) => $left,
            /** @param R $right */
            fn(mixed $right) => $right,
        );
    }

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
        return $this->getOrCall(fn() => $fallback);
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
        return $this->getOrCall(
        /** @param L $left */
            fn(mixed $left) => throw $fallback($left),
        );
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
     * @param callable(L): F $fallback
     * @return R|F
     */
    public function getOrCall(callable $fallback): mixed
    {
        return $this->fold(
            /** @param L $left */
            fn(mixed $left) => $fallback($left),
            /** @param R $right */
            fn(mixed $right) => $right,
        );
    }

    # endregion Destructors

    # region Refinements

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
     * @psalm-assert-if-true Left<L>&\Fp\Functional\Assertion $this
     * @psalm-assert-if-false Right<R>&\Fp\Functional\Assertion $this
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
     * @psalm-assert-if-true Right<R>&\Fp\Functional\Assertion $this
     * @psalm-assert-if-false Left<L>&\Fp\Functional\Assertion $this
     */
    public function isRight(): bool
    {
        return $this instanceof Right;
    }

    # endregion Refinements

    # region Castable

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
        return $this->fold(
            fn() => Option::none(),
            fn($r) => Option::some($r),
        );
    }

    /**
     * @return ArrayList<R>
     */
    public function toArrayList(): ArrayList
    {
        return $this->fold(
            fn() => ArrayList::empty(),
            fn($r) => ArrayList::singleton($r),
        );
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $value = ToStringOperation::of($this->get());

        return $this instanceof Left
            ? "Left({$value})"
            : "Right({$value})";
    }

    # endregion Castable

    # region Chainable

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
        return $this->fold(
            /** @param L $l */
            fn(mixed $l) => Either::left($l),
            /** @param R $r */
            fn(mixed $r) => Either::right($callback($r)),
        );
    }

    /**
     * Same as {@see Either::map()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template RO
     *
     * @param callable(mixed...): RO $callback
     * @return Either<L, RO>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function mapN(callable $callback): Either
    {
        return $this->map(function($tuple) use ($callback): mixed {
            /** @var array $tuple */
            return toSafeClosure($callback)(...$tuple);
        });
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
        return $this->fold(
            /** @param L $l */
            fn(mixed $l) => Either::left($callback($l)),
            /** @param R $r */
            fn(mixed $r) => Either::right($r),
        );
    }

    /**
     * 1) Unwrap the box
     * 2) If the box contains error value then do nothing
     * 3) Pass unwrapped success value to callback
     * 4) Return the same box
     *
     * ```php
     * >>> $res1 = Either::right(1);
     * => Right(1)
     *
     * >>> $res2 = $res1->tap(function (int $i) { echo $i; });
     * 1
     * => Right(1)
     *
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Right('1')
     * ```
     *
     * @param callable(R): void $callback
     * @return Either<L, R>
     */
    public function tap(callable $callback): Either
    {
        return $this->map(
            /** @param R $r */
            function(mixed $r) use ($callback) {
                $callback($r);
                return $r;
            },
        );
    }

    /**
     * Same as {@see Either::tap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): void $callback
     * @return Either<L, R>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function tapN(callable $callback): Either
    {
        return $this->tap(function($tuple) use ($callback) {
            /** @var array $tuple */
            toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * @param callable(L): void $callback
     * @return Either<L, R>
     */
    public function tapLeft(callable $callback): Either
    {
        return $this->mapLeft(
            /** @param L $l */
            function(mixed $l) use ($callback) {
                $callback($l);
                return $l;
            },
        );
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
        return $this->fold(
            /** @param L $l */
            fn(mixed $l) => Either::left($l),
            /** @param R $r */
            fn(mixed $r) => $callback($r),
        );
    }

    /**
     * Same as {@see Either::flatMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Either<LO|L, RO>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function flatMapN(callable $callback): Either
    {
        return $this->flatMap(function($tuple) use ($callback): Either {
            /** @var array $tuple */
            return toSafeClosure($callback)(...$tuple);
        });
    }

    /**
     * @template LO
     * @template RO
     *
     * @param callable(R): Either<LO, RO> $callback
     * @return Either<L|LO, R>
     */
    public function flatTap(callable $callback): Either
    {
        return $this->flatMap(
            /**
             * @param R $r
             * @psalm-suppress InvalidArgument
             */
            fn(mixed $r) => $callback($r)->fold(
                fn($l) => Either::left($l),
                fn() => Either::right($r),
            )
        );
    }

    /**
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Either<L|LO, R>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function flatTapN(callable $callback): Either
    {
        return $this->flatTap(function($tuple) use ($callback) {
            /** @var array $tuple */
            return toSafeClosure($callback)(...$tuple);
        });
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
     * @template LO
     * @template RO
     *
     * @param callable(): Either<LO, RO> $fallback
     * @return Either<L|LO, R|RO>
     */
    public function orElse(callable $fallback): Either
    {
        return $this->fold(fn() => $fallback(), fn() => $this);
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
        return $this->fold(
            /** @param L $l */
            fn(mixed $l) => Either::right($l),
            /** @param R $r */
            fn(mixed $r) => Either::left($r),
        );
    }

    /**
     * @template LO
     *
     * @param callable(R): bool $callback
     * @param callable(R): LO $left
     * @return Either<L|LO, R>
     */
    public function filterOrElse(callable $callback, callable $left): Either
    {
        return $this->fold(
            /** @param L $l */
            fn(mixed $l) => Either::left($l),
            /** @param R $r */
            fn(mixed $r) => $callback($r) ? Either::right($r) : Either::left($left($r)),
        );
    }

    # endregion Chainable

    #region Extension

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return EitherExtensions::call($this, $name, $arguments);
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return EitherExtensions::callStatic($name, $arguments);
    }

    #endregion Extension
}
