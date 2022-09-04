<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Error;
use Fp\Collections\ArrayList;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\WithExtensions;
use Fp\Operations\ToStringOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\OptionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\OptionGetReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\PluckMethodReturnTypeProvider;
use Generator;
use Throwable;

use function Fp\Evidence\proveOf;

/**
 * Option monad
 *
 * Represents optional computation.
 * Consists of {@see Some} and {@see None} subclasses.
 *
 * Prevents null pointer exceptions (NPE)
 * and allows short-circuiting the computation
 * if there was step which returned {@see None}.
 *
 * It's like a box. There can be something inside the box or not (empty box).
 *
 * Any call of {@see Option::map()} and {@see Option::flatMap()}
 * on {@see None} instance won't do anything
 * because of there is no sense to do something with "empty box"
 *
 * @template-covariant A
 * @psalm-yield A
 *
 * @psalm-seal-methods
 * @mixin OptionExtensions
 *
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Option
{
    use WithExtensions;

    # region Constructors

    /**
     * Fabric method for {@see None} instance
     *
     * ```php
     * >>> Option::none();
     * => None
     * ```
     *
     * @return Option<empty>
     */
    public static function none(): Option
    {
        return None::getInstance();
    }

    /**
     * Fabric method for {@see Some} instance
     *
     * ```php
     * >>> Option::some(1);
     * => Some(1)
     * ```
     *
     * @template B
     *
     * @param B $value
     * @return Option<B>
     */
    public static function some(mixed $value): Option
    {
        return new Some($value);
    }

    /**
     * Fabric method which creates Option.
     *
     * Try/catch replacement.
     *
     * ```php
     * >>> Option::try(fn() => 1);
     * => Some(1)
     *
     * >>> Option::try(fn() => throw new Exception('handled and converted to None'));
     * => None
     * ```
     *
     * @template B
     *
     * @param callable(): B $callback
     * @return Option<B>
     */
    public static function try(callable $callback): Option
    {
        try {
            return Option::some($callback());
        } catch (Throwable) {
            return Option::none();
        }
    }

    /**
     * Fabric method which creates Option.
     *
     * Wrap given value into Option context.
     *
     * Creates {@see None} for null values
     * and {@see Some} for not null values.
     *
     * ```php
     * >>> Option::fromNullable(2);
     * => Some(2)
     *
     * >>> Option::fromNullable(null);
     * => None
     * ```
     *
     * @template B
     *
     * @param B|null $value
     * @return Option<B>
     */
    public static function fromNullable(mixed $value): Option
    {
        return null !== $value
            ? Option::some($value)
            : Option::none();
    }

    /**
     * Do-notation a.k.a. for-comprehension.
     *
     * Syntax sugar for sequential {@see Option::flatMap()} calls
     *
     * Syntax "$unwrappedValue = yield $box" mean:
     * 1) unwrap the $box
     * 2) if there is nothing in the box then short-circuit (stop) the computation
     * 3) place contained in $box value into $unwrappedValue variable
     *
     * ```php
     * >>> Option::do(function() {
     *     $a = 1;
     *     $b = yield Option::fromNullable(2);
     *     $c = yield Option::some(3);
     *     $d = yield Option::none();   // short circuit here
     *     $e = 5;                      // not executed
     *     return [$a, $b, $c, $d, $e]; // not executed
     * });
     * => None
     * ```
     *
     * @todo Replace Option<mixed> with Option<TS> and drop suppress @see https://github.com/vimeo/psalm/issues/6288
     *
     * @template TS
     * @template TO
     *
     * @param callable(): Generator<int, Option<mixed>, TS, TO> $computation
     * @return Option<TO>
     */
    public static function do(callable $computation): Option {
        $generator = $computation();

        while ($generator->valid()) {
            $currentStep = $generator->current();

            if ($currentStep->isSome()) {
                /** @psalm-suppress MixedArgument */
                $generator->send($currentStep->get());
            } else {
                /** @var Option<TO> $currentStep */
                return $currentStep;
            }

        }

        return Option::some($generator->getReturn());
    }

    # endregion Constructors

    # region Destructors

    /**
     * Fold possible outcomes
     *
     * ```php
     * >>> Option::fromNullable(1)->fold(
     *     ifSome: fn(int $some) => $some + 1,
     *     ifNone: fn() => 0,
     * );
     * => 2
     *
     * >>> Option::fromNullable(null)->fold(
     *     ifSome: fn(int $some) => $some + 1,
     *     ifNone: fn() => 0,
     * );
     * => 0
     * ```
     *
     * @template SO
     * @template NO
     *
     * @param callable(): NO $ifNone
     * @param callable(A): SO $ifSome
     * @return SO|NO
     */
    public function fold(callable $ifNone, callable $ifSome): mixed
    {
        return $this->isNone() ? $ifNone() : $ifSome($this->get());
    }

    /**
     * Unwrap "the box" and get contained value
     * or null for empty box case
     *
     * ```php
     * >>> Option::some(1)->get()
     * => 1
     *
     * >>> Option::none()->get()
     * => null
     * ```
     *
     * @return A|null
     *
     * @see OptionGetReturnTypeProvider
     */
    public abstract function get(): mixed;

    /**
     * Unwrap "the box" and get contained value
     * or throw exception for empty box case
     *
     * ```php
     * >>> Option::some(1)->get()
     * => 1
     *
     * >>> Option::none()->get()
     * PHP Error: Trying to get value of None
     * ```
     *
     * @return A
     */
    public function getUnsafe(): mixed
    {
        return $this->isSome()
            ? $this->get()
            : throw new Error("Trying to get value of None");
    }

    /**
     * Unwrap "the box" and get contained value
     * or given fallback value for empty box case
     *
     * ```php
     * >>> Option::some(1)->getOrElse(0);
     * => 1
     *
     * >>> Option::none()->getOrElse(0);
     * => 0
     * ```
     *
     * @template B
     *
     * @param B $fallback
     * @return A|B
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->isSome()
            ? $this->get()
            : $fallback;
    }

    /**
     * Unwrap "the box" and get contained value
     * or return callable result as fallback value for empty box case
     *
     * ```php
     * >>> Option::some(1)->getOrCall(fn() => 0);
     * => 1
     *
     * >>> Option::none()->getOrCall(fn() => 0);
     * => 0
     * ```
     *
     * @template B
     *
     * @param callable(): B $fallback
     * @return A|B
     */
    public function getOrCall(callable $fallback): mixed
    {
        return $this->isSome()
            ? $this->get()
            : $fallback();
    }

    # endregion Destructors

    # region Refinements

    /**
     * Check if there is something inside the box
     *
     * ```php
     * >>> Option::fromNullable(null)->isSome();
     * => false
     * ```
     *
     * @psalm-assert-if-true Some<A>&\Fp\Functional\Assertion<"must-be-some"> $this
     */
    public function isSome(): bool
    {
        return $this instanceof Some;
    }

    /**
     * Check if "the box" is empty
     *
     * ```php
     * >>> Option::fromNullable(null)->isNone();
     * => true
     * ```
     *
     * @psalm-assert-if-true None&\Fp\Functional\Assertion<"must-be-none"> $this
     */
    public function isNone(): bool
    {
        return $this instanceof None;
    }

    # endregion Refinements

    # region Castable

    /**
     * Convert {@see Option} to {@see Either}
     *
     * {@see Some} to {@see Left}
     * {@see None} to {@see Right}
     *
     * ```php
     * >>> Option::some('error')->toLeft(fn() => 1);
     * => Left('error')
     *
     * >>> Option::none()->toLeft(fn() => 1);
     * => Right(1)
     * ```
     *
     * @template B
     *
     * @param callable(): B $right
     * @return Either<A, B>
     */
    public function toLeft(callable $right): Either
    {
        return $this->isSome()
            ? Either::left($this->get())
            : Either::right($right());
    }

    /**
     * Convert {@see Option} to {@see Either}
     *
     * {@see Some} to {@see Right}
     * {@see None} to {@see Left}
     *
     * ```php
     * >>> Option::some(1)->toRight(fn() => 'error');
     * => Right(1)
     *
     * >>> Option::none()->toRight(fn() => 'error');
     * => Left('error')
     * ```
     *
     * @template B
     *
     * @param callable(): B $left
     * @return Either<B, A>
     */
    public function toRight(callable $left): Either
    {
        return $this->isSome()
            ? Either::right($this->get())
            : Either::left($left());
    }

    /**
     * Convert Option<A> to ArrayList<A> with 0 or 1 element.
     *
     * ```php
     * >>> Option::some(1)->toArrayList();
     * => ArrayList(1)
     *
     * >>> Option::none()->toArrayList();
     * => ArrayList()
     * ```
     *
     * @return ArrayList<A>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->isSome() ? [$this->get()] : []);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $value = ToStringOperation::of($this->get());

        return $this instanceof None ? 'None' : "Some({$value})";
    }

    # endregion Castable

    # region Chainable

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Place callback result value into the new box
     *
     * ```php
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     *
     * >>> $res2 = $res1->map(fn(int $i) => $i + 1);
     * => Some(2)
     *
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Some('2')
     * ```
     *
     * @template B
     *
     * @param callable(A): (B) $callback
     * @return Option<B>
     */
    public function map(callable $callback): Option
    {
        return $this->isSome()
            ? Option::some($callback($this->get()))
            : Option::none();
    }

    /**
     * Same as {@see Option::map()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * ```php
     * >>> $res1 = Option::some([1, 2, 3]);
     * => Some([1, 2, 3])
     *
     * >>> $res2 = $res1->map(fn(int $a, int $b, int $c) => $a + $b + $c);
     * => Some(6)
     * ```
     *
     * @template B
     *
     * @param callable(mixed...): B $callback
     * @return Option<B>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function mapN(callable $callback): Option
    {
        return $this->map(function($tuple) use ($callback): mixed {
            /** @var array $tuple */
            return $callback(...$tuple);
        });
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Return the same box
     *
     * ```php
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     *
     * >>> $res2 = $res1->tap(function (int $i) { echo $i; });
     * 1
     * => Some(1)
     *
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Some('1')
     * ```
     *
     * @param callable(A): void $callback
     * @return Option<A>
     */
    public function tap(callable $callback): Option
    {
        if ($this->isSome()) {
            $callback($this->get());
            return $this;
        } else {
            return Option::none();
        }
    }

    /**
     * Same as {@see Option::tap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * ```php
     * >>> $res1 = Option::some([1, 2, 3]);
     * => Some([1, 2, 3])
     *
     * >>> $res2 = $res1->tapN(function (int $a, int $b, int $c) { print_r([$a, $b, $c]); });
     * [1, 2, 3]
     * => Some([1, 2, 3])
     * ```
     *
     * @param callable(mixed...): void $callback
     * @return Option<A>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function tapN(callable $callback): Option
    {
        return $this->tap(function($tuple) use ($callback) {
            /** @var array $tuple */
            $callback(...$tuple);
        });
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Replace old box with new one returned by callback
     *
     * ```php
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     *
     * >>> $res2 = $res1->flatMap(fn(int $i) => Option::some($i + 1));
     * => Some(2)
     *
     * >>> $res3 = $res2->flatMap(fn(int $i) => Option::none());
     * => None
     * ```
     *
     * @template B
     *
     * @param callable(A): Option<B> $callback
     * @return Option<B>
     */
    public function flatMap(callable $callback): Option
    {
        return $this->isSome()
            ? $callback($this->get())
            : Option::none();
    }

    /**
     * Same as {@see Option::flatMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * ```php
     * >>> $res1 = Option::some([1, 2, 3]);
     * => Some([1, 2, 3])
     *
     * >>> $res2 = $res1->flatMapN(fn(int $a, int $b, int $c) => Option::some($a + $b + $c));
     * => Some(6)
     * ```
     *
     * @template B
     *
     * @param callable(mixed...): Option<B> $callback
     * @return Option<B>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function flatMapN(callable $callback): Option
    {
        return $this->flatMap(function($tuple) use ($callback): Option {
            /** @var array $tuple */
            return $callback(...$tuple);
        });
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) If the callback returns empty box then short circuit the execution
     * 5) Return the same box
     *
     * ```php
     * >>> $res1 = Option::fromNullable([1]);
     * => Some([1])
     *
     * >>> $res2 = $res1->flatTap(fn(array $arr): Option => first($arr));
     * => Some([1])
     *
     * >>> $res3 = $res2->flatTap(fn(array $arr): Option => second($arr))
     * => None
     * ```
     *
     * @template B
     *
     * @param callable(A): Option<B> $callback
     * @return Option<A>
     */
    public function flatTap(callable $callback): Option
    {
        if ($this->isSome() && $callback($this->get())->isSome()) {
            return $this;
        } else {
            return Option::none();
        }
    }

    /**
     * Same as {@see Option::flatTap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template B
     *
     * @param callable(mixed...): Option<B> $callback
     * @return Option<A>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function flatTapN(callable $callback): Option
    {
        return $this->flatTap(function($tuple) use ($callback): Option {
            /** @var array $tuple */
            return $callback(...$tuple);
        });
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) If callback return true, return Some<A>. Otherwise None.
     *
     * ```php
     * >>> $res1 = Option::fromNullable(42);
     * => Some(42)
     *
     * >>> $res2 = $res1->filter(fn(int $i) => $i >= 42);
     * => Some(42)
     *
     * >>> $res3 = $res2->filter(fn(int $i) => $i > 42);
     * => None
     * ```
     *
     * @param callable(A): bool $callback
     * @return Option<A>
     *
     * @see OptionFilterMethodReturnTypeProvider
     */
    public function filter(callable $callback): Option
    {
        return $this->isSome() && $callback($this->get())
            ? $this
            : Option::none();
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Check if unwrapped value is of given class
     * 4) If the value is of given class then return Some. Otherwise, None.
     *
     * ```php
     * >>> $res1 = Option::fromNullable(new Foo(1));
     * => Some(Foo(1))
     *
     * >>> $res2 = $res1->filterOf(Foo::class);
     * => Some(Foo(1))
     *
     * >>> $res3 = $res2->filterOf(Bar::class);
     * => None
     * ```
     *
     * @template B
     *
     * @param class-string<B>|list<class-string<B>> $fqcn
     * @return Option<B>
     */
    public function filterOf(string|array $fqcn, bool $invariant = false): Option
    {
        return $this->isSome()
            ? proveOf($this->get(), $fqcn, $invariant)
            : Option::none();
    }

    /**
     * Combine two Options into one
     *
     * ```php
     * >>> Option::some(1)->orElse(fn() => Option::some(2));
     * => Some(1)
     *
     * >>> Option::none()->orElse(fn() => Option::some(2));
     * => Some(2)
     * ```
     *
     * @template B
     *
     * @param callable(): Option<B> $fallback
     * @return Option<A|B>
     */
    public function orElse(callable $fallback): Option
    {
        return $this->isSome()
            ? $this
            : $fallback();
    }

    /**
     * Shortcut for {@see Option::map()}
     *
     * ```php
     * >>> Option::some(['a' => 1])->pluck('a')
     * => Some(1)
     * >>> Option::some(new Foo(a: 1))->pluck('a')
     * => Some(1)
     * ```
     *
     * @return Option<mixed>
     *
     * @see PluckMethodReturnTypeProvider
     */
    public function pluck(string $key): Option
    {
        return $this->map(fn($item) => match (true) {
            is_object($item) => $item->{$key} ?? null,
            is_array($item) => $item[$key] ?? null,
        });
    }

    # endregion Chainable
}
