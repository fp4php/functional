<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Closure;
use Generator;

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

//    /**
//     * @psalm-template TI1
//     * @psalm-template TI2
//     * @psalm-template TO
//     *
//     * @psalm-param Option<TI1> $o1
//     * @psalm-param Option<TI2> $o2
//     * @psalm-param Closure(TI1, TI2): Option<TO> $closure
//     *
//     * @psalm-return Option<TO>
//     */
//    public static function flatMap2(Option $o1, Option $o2, Closure $closure): Option
//    {
//        return $o1->flatMap(fn($v1) => $o2->flatMap(fn($v2) => $closure($v1, $v2)));
//    }

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
     * @psalm-return A|null
     */
    public abstract function get(): mixed;

    /**
     * @psalm-param A $fallback
     * @psalm-return A
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->get() ?? $fallback;
    }
}
