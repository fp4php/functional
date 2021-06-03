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
 * @implements Semigroup<Validated<E, A>>
 * @psalm-immutable
 */
abstract class Validated implements Semigroup
{
    /**
     * @psalm-template EE
     * @psalm-template AA
     *
     * @psalm-param EE $value
     *
     * @psalm-return Invalid<EE>
     * @psalm-pure
     */
    public static function invalid(mixed $value): Invalid
    {
        return new Invalid([$value]);
    }

    /**
     * @psalm-template EE
     * @psalm-template AA
     *
     * @psalm-param Semigroup<AA> $semi
     *
     * @psalm-return Valid<AA>
     * @psalm-pure
     */
    public static function valid(Semigroup $semi): Valid
    {
        return new Valid($semi);
    }

    /**
     * @psalm-return non-empty-list<E>|A
     */
    abstract public function get(): mixed;

    /**
     * @return Validated<E, A>
     */
    public function extract(): Validated
    {
        return $this;
    }

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
     * @psalm-param Validated<E, A> $rhs
     * @psalm-return Validated<E, A>
     */
    public function combineOne(mixed $rhs): Validated
    {
        if ($this->isValid() && $rhs->isValid()) {
            /**
             * @var Valid<A> $this
             */
            return new Valid($this->combineOneSemi());
            return new Valid($rhs->semi, $this->semi->combineOne($rhs->value));
        }

        if ($this->isInvalid() && $rhs->isInvalid()) {
            /**
             * @var Invalid<E> $this
             */
            return new Invalid($this->semi->combineOne($rhs->value));
        }

        return $rhs->isInvalid() ? $rhs : $this;
    }


    /**
     * @psalm-template EE
     * @psalm-template AA
     *
     * @psalm-param Semigroup<AA> $semi
     * @psalm-param EE $left
     * @psalm-param AA $right
     *
     * @psalm-return Validated<EE, AA>
     *
     * @psalm-pure
     */
    public static function cond(
        Semigroup $semi,
        bool $condition,
        mixed $valid,
        mixed $invalid,
    ): Validated
    {
        return $condition
            ? self::valid($semi, $valid)
            : self::invalid($invalid);
    }


    /**
     * @psalm-template B
     *
     * @param callable(A): B $ifValid
     * @param callable(non-empty-list<E>): B $ifInvalid
     *
     * @return B
     */
    public function fold(callable $ifValid, callable $ifInvalid): mixed
    {
        if ($this->isValid()) {
            return call_user_func($ifValid, $this->value);
        }

        /**
         * @var Invalid<E, A> $this
         */

        return call_user_func($ifInvalid, $this->value);
    }

    /**
     * @psalm-return Either<non-empty-list<E>, A>
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
