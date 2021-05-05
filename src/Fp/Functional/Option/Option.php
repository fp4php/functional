<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

use Closure;

/**
 * @template-covariant A
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
     * @template T
     * @psalm-param T $fallback
     * @psalm-return A|T
     */
    public function getOrElse(mixed $fallback): mixed
    {
        return $this->get() ?? $fallback;
    }
}
