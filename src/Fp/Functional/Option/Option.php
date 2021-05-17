<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Closure;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Generator;
use Throwable;

/**
 * @template-covariant A
 * @psalm-yield A
 * @psalm-immutable
 */
abstract class Option
{
    /**
     * @psalm-assert-if-false Some<A> $this
     */
    public function isEmpty(): bool
    {
        return $this instanceof None;
    }

    /**
     * @psalm-template B
     * @param Closure(A): (B|null) $closure
     * @psalm-return Option<B>
     */
    public function map(Closure $closure): Option
    {
        if ($this->isEmpty()) {
            return new None();
        }

        $value = $this->value;

        $result = $closure($value);

        return is_null($result) ? new None() : new Some($result);
    }

    /**
     * @psalm-template B
     * @param Closure(A): Option<B> $closure
     * @psalm-return Option<B>
     */
    public function flatMap(Closure $closure): Option
    {
        if ($this->isEmpty()) {
            return new None();
        }

        $value = $this->value;

        return $closure($value);
    }

    /**
     * @template TS
     * @template TO
     * @psalm-param callable(): Generator<int, Option<TS>, TS, TO> $computation
     * @psalm-return Option<TO>
     */
    public static function do(callable $computation): Option {
        $generator = $computation();

        while ($generator->valid()) {
            $currentStep = $generator->current();

            if (!$currentStep->isEmpty()) {
                $generator->send($currentStep->get());
            } else {
                /** @var Option<TO> $currentStep */
                return $currentStep;
            }

        }

        return Option::of($generator->getReturn());
    }

    /**
     * @psalm-template B
     * @param B|null $value
     * @psalm-return Option<B>
     */
    public static function of(mixed $value): Option
    {
        return is_null($value) ? new None() : new Some($value);
    }

    /**
     * @psalm-template B
     * @psalm-param (callable(): (B|null)) $callback
     * @psalm-return Option<B>
     */
    public static function try(callable $callback): Option
    {
        try {
            return self::of(call_user_func($callback));
        } catch (Throwable) {
            return new None();
        }
    }

    /**
     * @psalm-template B
     * @psalm-param \Closure(A): B $ifSome
     * @psalm-param \Closure(): B $ifNone
     * @psalm-return B
     */
    public function fold(\Closure $ifSome, \Closure $ifNone): mixed
    {
        return !$this->isEmpty()
            ? $ifSome($this->value)
            : $ifNone();
    }

    /**
     * @psalm-return A|null
     */
    public abstract function get(): mixed;

    /**
     * @psalm-template B
     * @psalm-param B $fallback
     * @psalm-return A|B
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return !$this->isEmpty()
            ? $this->value
            : $fallback;
    }

    /**
     * Fabric method
     *
     * @psalm-template B
     * @psalm-param B $value
     * @psalm-return Some<B>
     */
    public static function some(int|float|bool|string|object|array $value): Some
    {
        return new Some($value);
    }

    /**
     * Fabric method
     *
     * @psalm-return None
     */
    public static function none(): None
    {
        return new None();
    }

    /**
     * @psalm-template B
     * @psalm-param callable(): B $right
     * @psalm-return Either<A, B>
     */
    public function toLeft(callable $right): Either
    {
        return !$this->isEmpty()
            ? new Left($this->value)
            : new Right(call_user_func($right));
    }

    /**
     * @psalm-template B
     * @psalm-param callable(): B $left
     * @psalm-return Either<B, A>
     */
    public function toRight(callable $left): Either
    {
        return !$this->isEmpty()
            ? new Right($this->value)
            : new Left(call_user_func($left));
    }
}
