<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Functional\Semigroup\Semigroup;

/**
 * @template-covariant E
 * @template-covariant A
 * @psalm-immutable
 */
abstract class Validated
{
    /**
     * @psalm-template EE
     *
     * @psalm-param EE $value
     *
     * @psalm-return Invalid<EE>
     * @psalm-pure
     */
    public static function invalid(mixed $value): Invalid
    {
        return new Invalid($value);
    }

    /**
     * @psalm-template AA
     *
     * @psalm-param Semigroup<AA> $semi
     *
     * @psalm-return Valid<AA>
     * @psalm-pure
     */
    public static function valid(mixed $value): Valid
    {
        return new Valid($value);
    }

    /**
     * @psalm-return E|A
     */
    abstract public function get(): mixed;

    /**
     * @psalm-assert-if-true Valid<A> $this
     */
    public function isValid(): bool
    {
        return $this instanceof Valid;
    }

    /**
     * @psalm-assert-if-true Invalid<E> $this
     */
    public function isInvalid(): bool
    {
        return $this instanceof Invalid;
    }

    /**
     * @psalm-template EE
     * @psalm-template AA
     *
     * @psalm-param EE $left
     * @psalm-param AA $right
     *
     * @psalm-return Validated<EE, AA>
     *
     * @psalm-pure
     */
    public static function cond(
        bool $condition,
        mixed $valid,
        mixed $invalid,
    ): Validated
    {
        return $condition
            ? self::valid($valid)
            : self::invalid($invalid);
    }


    /**
     * @psalm-template B
     *
     * @psalm-param callable(A): B $ifValid
     * @psalm-param callable(E): B $ifInvalid
     *
     * @psalm-return B
     */
    public function fold(callable $ifValid, callable $ifInvalid): mixed
    {
        if ($this->isValid()) {
            return call_user_func($ifValid, $this->value);
        }

        /**
         * @var Invalid<E> $this
         */

        return call_user_func($ifInvalid, $this->value);
    }

    /**
     * @psalm-return Either<E, A>
     */
    public function toEither(): Either
    {
        if ($this->isValid()) {
            $value = $this->value;
            return new Right($value);
        }

        /**
         * @var Invalid<E> $this
         */

        $value = $this->value;

        return new Left($value);
    }

    /**
     * @psalm-return Option<A>
     */
    public function toOption(): Option
    {
        return $this->isValid()
            ? new Some($this->value)
            : new None();
    }
}
