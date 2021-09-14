<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Error;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Generator;
use Throwable;

use function Fp\objectOf;

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
 * @psalm-immutable
 */
abstract class Option
{
    /**
     * Check if "the box" is empty
     *
     * REPL:
     * >>> Option::fromNullable(null)->isEmpty();
     * => true
     *
     * @psalm-assert-if-false Some<A> $this
     */
    public function isEmpty(): bool
    {
        return $this instanceof None;
    }

    /**
     * Check if there is something inside the box
     *
     * REPL:
     * >>> Option::fromNullable(null)->isNonEmpty();
     * => false
     *
     * @psalm-assert-if-true Some<A> $this
     */
    public function isNonEmpty(): bool
    {
        return $this instanceof Some;
    }

    /**
     * Check if there is something inside the box
     * Alias for {@see Option::isNonEmpty()}
     *
     * REPL:
     * >>> Option::fromNullable(null)->isSome();
     * => false
     *
     * @psalm-assert-if-true Some<A> $this
     */
    public function isSome(): bool
    {
        return $this instanceof Some;
    }

    /**
     * Check if "the box" is empty
     * Alias for {@see Option::isEmpty()}
     *
     * REPL:
     * >>> Option::fromNullable(null)->isNone();
     * => true
     *
     * @psalm-assert-if-false Some<A> $this
     */
    public function isNone(): bool
    {
        return $this instanceof None;
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Place callback result value into the new box
     *
     * REPL:
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     * >>> $res2 = $res1->map(fn(int $i) => $i + 1);
     * => Some(2)
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Some('2')
     *
     * @psalm-template B
     * @param callable(A): (B) $callback
     * @psalm-return Option<B>
     */
    public function map(callable $callback): Option
    {
        return $this->isSome()
            ? self::some($callback($this->value))
            : self::none();
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Return the same box
     *
     * REPL:
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     * >>> $res2 = $res1->tap(function (int $i) { echo $i; });
     * 1
     * => Some(1)
     * >>> $res3 = $res2->map(fn(int $i) => (string) $i);
     * => Some('1')
     *
     * @param callable(A): void $callback
     * @psalm-return Option<A>
     */
    public function tap(callable $callback): Option
    {
        if ($this->isSome()) {
            $callback($this->value);
            return $this;
        } else {
            return self::none();
        }
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) If the callback returns empty box then short circuit the execution
     * 5) Return the same box
     *
     * REPL:
     * >>> $res1 = Option::fromNullable([1]);
     * => Some([1])
     * >>> $res2 = $res1->flatTap(fn(array $arr): Option => first($arr));
     * => Some([1])
     * >>> $res3 = $res2->flatTap(fn(array $arr): Option => second($arr))
     * => None
     *
     * @template B
     * @param callable(A): Option<B> $callback
     * @psalm-return Option<A>
     */
    public function flatTap(callable $callback): Option
    {
        if ($this->isSome() && $callback($this->value)->isSome()) {
            $callback($this->value);
            return $this;
        } else {
            return self::none();
        }
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) If callback return true, return Some<A>. Otherwise None.
     *
     * REPL:
     * >>> $res1 = Option::fromNullable(42);
     * => Some(42)
     * >>> $res2 = $res1->filter(fn(int $i) => $i >= 42);
     * => Some(42)
     * >>> $res3 = $res2->filter(fn(int $i) => $i > 42);
     * => None
     *
     * @param callable(A): bool $callback
     * @return Option<A>
     */
    public function filter(callable $callback): Option
    {
        return $this->isSome() && $callback($this->value)
            ? self::some($this->value)
            : self::none();
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Check if unwrapped value is of given class
     * 4) If the value is of given class then return Some. Otherwise, None.
     *
     * REPL:
     * >>> $res1 = Option::fromNullable(new Foo(1));
     * => Some(Foo(1))
     * >>> $res2 = $res1->filterOf(Foo::class);
     * => Some(Foo(1))
     * >>> $res3 = $res2->filterOf(Bar::class);
     * => None
     *
     * @template AA
     * @param class-string<AA> $fqcn
     * @return Option<AA>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Option
    {
        /** @var Option<AA> */
        return $this->isSome() && objectOf($this->value, $fqcn, $invariant)
            ? self::some($this->value)
            : self::none();
    }

    /**
     * 1) Unwrap the box
     * 2) If the box is empty then do nothing
     * 3) Pass unwrapped value to callback
     * 4) Replace old box with new one returned by callback
     *
     * REPL:
     * >>> $res1 = Option::fromNullable(1);
     * => Some(1)
     * >>> $res2 = $res1->flatMap(fn(int $i) => Option::some($i + 1));
     * => Some(2)
     * >>> $res3 = $res2->flatMap(fn(int $i) => Option::none());
     * => None
     *
     * @psalm-template B
     * @param callable(A): Option<B> $callback
     * @psalm-return Option<B>
     */
    public function flatMap(callable $callback): Option
    {
        return $this->isSome()
            ? $callback($this->value)
            : self::none();
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
     * REPL:
     * >>> Option::do(function() {
     *     $a = 1;
     *     $b = yield Option::fromNullable(2);
     *     $c = yield Option::some(3);
     *     $d = yield Option::none();   // short circuit here
     *     $e = 5;                      // not executed
     *     return [$a, $b, $c, $d, $e]; // not executed
     * });
     * => None
     *
     * @todo Replace Option<mixed> with Option<TS> and drop suppress @see https://github.com/vimeo/psalm/issues/6288
     *
     * @template TS
     * @template TO
     * @psalm-param callable(): Generator<int, Option<mixed>, TS, TO> $computation
     * @psalm-return Option<TO>
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

    /**
     * Fabric method which creates Option.
     *
     * Wrap given value into Option context.
     *
     * Creates {@see None} for null values
     * and {@see Some} for not null values.
     *
     * REPL:
     * >>> Option::fromNullable(2);
     * => Some(2)
     * >>> Option::fromNullable(null);
     * => None
     *
     * @psalm-template B
     * @psalm-param B|null $value
     * @psalm-return Option<B>
     * @psalm-pure
     */
    public static function fromNullable(mixed $value): Option
    {
        return null !== $value
            ? self::some($value)
            : self::none();
    }

    /**
     * Fabric method which creates Option.
     *
     * Try/catch replacement.
     *
     * REPL:
     * >>> Option::try(fn() => 1);
     * => Some(1)
     * >>> Option::try(fn() => throw new Exception('handled and converted to None'));
     * => None
     *
     * @psalm-template B
     * @psalm-param callable(): B $callback
     * @psalm-return Option<B>
     */
    public static function try(callable $callback): Option
    {
        try {
            return self::some($callback());
        } catch (Throwable) {
            return self::none();
        }
    }

    /**
     * Fold possible outcomes
     *
     * REPL:
     * >>> Option::fromNullable(1)->fold(
     *     ifSome: fn(int $some) => $some + 1,
     *     ifNone: fn() => 0,
     * );
     * => 2
     * >>> Option::fromNullable(null)->fold(
     *     ifSome: fn(int $some) => $some + 1,
     *     ifNone: fn() => 0,
     * );
     * => 0
     *
     * @psalm-template B
     * @psalm-param callable(A): B $ifSome
     * @psalm-param callable(): B $ifNone
     * @psalm-return B
     */
    public function fold(callable $ifSome, callable $ifNone): mixed
    {
        return $this->isSome()
            ? $ifSome($this->value)
            : $ifNone();
    }

    /**
     * Unwrap "the box" and get contained value
     * or null for empty box case
     *
     * REPL:
     * >>> Option::some(1)->get()
     * => 1
     * >>> Option::none()->get()
     * => null
     *
     * @psalm-return A|null
     */
    public abstract function get(): mixed;

    /**
     * Unwrap "the box" and get contained value
     * or throw exception for empty box case
     *
     * REPL:
     * >>> Option::some(1)->get()
     * => 1
     * >>> Option::none()->get()
     * => RuntimeException
     *
     * @psalm-return A
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
     * REPL:
     * >>> Option::some(1)->getOrElse(0)
     * => 1
     * >>> Option::none()->getOrElse(0)
     * => 0
     *
     * @psalm-template B
     * @psalm-param B $fallback
     * @psalm-return A|B
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->isSome()
            ? $this->value
            : $fallback;
    }

    /**
     * Unwrap "the box" and get contained value
     * or return callable result as fallback value for empty box case
     *
     * REPL:
     * >>> Option::some(1)->getOrCall(fn() => 0)
     * => 1
     * >>> Option::none()->getOrElse(fn() => 0)
     * => 0
     *
     * @psalm-template B
     * @psalm-param callable(): B $fallback
     * @psalm-return A|B
     */
    public function getOrCall(callable $fallback): mixed
    {
        return $this->isSome()
            ? $this->value
            : $fallback();
    }

    /**
     * Combine two Options into one
     *
     * REPL:
     * >>> Option::some(1)->orElse(fn() => Option::some(2))
     * => Some(1)
     * >>> Option::none()->orElse(fn() => Option::some(2))
     * => Some(2)
     *
     * @psalm-template B
     * @psalm-param callable(): Option<B> $fallback
     * @psalm-return Option<A|B>
     */
    public function orElse(callable $fallback): Option
    {
        return $this->isSome()
            ? $this
            : $fallback();
    }

    /**
     * Fabric method for {@see Some} instance
     *
     * REPL:
     * >>> Option::some(1)
     * => Some(1)
     *
     * @psalm-template B
     * @psalm-param B $value
     * @psalm-return Option<B>
     * @psalm-pure
     */
    public static function some(mixed $value): Option
    {
        return new Some($value);
    }

    /**
     * Fabric method for {@see None} instance
     *
     * REPL:
     * >>> Option::none()
     * => None
     *
     * @psalm-return Option<empty>
     * @psalm-pure
     */
    public static function none(): Option
    {
        return None::getInstance();
    }

    /**
     * Convert {@see Option} to {@see Either}
     *
     * {@see Some} to {@see Left}
     * {@see None} to {@see Right}
     *
     * REPL:
     * >>> Option::some('error')->toLeft(fn() => 1)
     * => Left('error')
     * >>> Option::none()->toLeft(fn() => 1)
     * => Right(1)
     *
     * @psalm-template B
     * @psalm-param callable(): B $right
     * @psalm-return Either<A, B>
     */
    public function toLeft(callable $right): Either
    {
        return $this->isSome()
            ? Either::left($this->value)
            : Either::right($right());
    }

    /**
     * Convert {@see Option} to {@see Either}
     *
     * {@see Some} to {@see Right}
     * {@see None} to {@see Left}
     *
     * REPL:
     * >>> Option::some(1)->toRight(fn() => 'error')
     * => Right(1)
     * >>> Option::none()->toRight(fn() => 'error')
     * => Left('error')
     *
     * @psalm-template B
     * @psalm-param callable(): B $left
     * @psalm-return Either<B, A>
     */
    public function toRight(callable $left): Either
    {
        return $this->isSome()
            ? Either::right($this->value)
            : Either::left($left());
    }

    /**
     * Fabric method.
     *
     * REPL:
     * >>> Option::cond(true, 1)
     * => Some(1)
     * >>> Option::cond(false, 1)
     * => None
     *
     * @psalm-pure
     * @psalm-template AI
     * @psalm-param AI $some
     * @psalm-return Option<AI>
     */
    public static function cond(bool $condition, mixed $some): Option
    {
        return $condition
            ? self::some($some)
            : self::none();
    }

    /**
     * Fabric method.
     *
     * REPL:
     * >>> Option::condLazy(true, fn() => 1)
     * => Some(1)
     * >>> Option::condLazy(false, fn() => 1)
     * => None
     *
     * Create {@see Some} from value if given condition is true
     * Create {@see None} if given condition is false
     *
     * @psalm-pure
     * @psalm-template AI
     * @psalm-param pure-callable(): AI $some
     * @psalm-return Option<AI>
     */
    public static function condLazy(bool $condition, callable $some): Option
    {
        return $condition
            ? self::some($some())
            : self::none();
    }
}
