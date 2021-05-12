<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Closure;
use Generator;
use Throwable;

/**
 * @template-covariant A
 * @psalm-yield A
 * @psalm-immutable
 */
abstract class Option
{
    protected function __construct(
        /**
         * @var A
         */
        protected mixed $value
    ) {
    }

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

        /** @psalm-var A $value */
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

        /** @psalm-var A $value */
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

        do {
            $currentStep = $generator->current();

            if (!$currentStep->isEmpty()) {
                $generator->send($currentStep->get());
            } else {
                /** @var Option<TO> $currentStep */
                return $currentStep;
            }

        } while ($generator->valid());

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
     * @template T
     * @psalm-param \Closure(A): T $ifSome
     * @psalm-param \Closure(): T $ifNone
     * @return T
     */
    public function fold(\Closure $ifSome, \Closure $ifNone): mixed
    {
        if (!$this->isEmpty()) {
            return $ifSome($this->get());
        } else {
            return $ifNone();
        }
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
        return $this->get() ?? $fallback;
    }
}
