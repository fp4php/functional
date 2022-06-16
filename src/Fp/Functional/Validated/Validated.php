<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;

/**
 * @template-covariant E
 * @template-covariant A
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Validated
{
    /**
     * @psalm-template EE
     * @psalm-param EE $value
     * @psalm-return Validated<EE, empty>
     */
    public static function invalid(mixed $value): Validated
    {
        return new Invalid($value);
    }

    /**
     * @psalm-template AA
     * @psalm-param AA $value
     * @psalm-return Validated<empty, AA>
     */
    public static function valid(mixed $value): Validated
    {
        return new Valid($value);
    }

    /**
     * @psalm-return E|A
     */
    abstract public function get(): mixed;

    /**
     * @psalm-assert-if-true Valid<A>&\Fp\Functional\Assertion<"must-be-valid"> $this
     */
    public function isValid(): bool
    {
        return $this instanceof Valid;
    }

    /**
     * @psalm-assert-if-true Invalid<E>&\Fp\Functional\Assertion<"must-be-invalid"> $this
     */
    public function isInvalid(): bool
    {
        return $this instanceof Invalid;
    }

    /**
     * @psalm-template EE
     * @psalm-template AA
     * @psalm-param EE $invalid
     * @psalm-param AA $valid
     * @psalm-return Validated<EE, AA>
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
     * @psalm-template EE
     * @psalm-template AA
     * @psalm-param callable(): EE $invalid
     * @psalm-param callable(): AA $valid
     * @psalm-return Validated<EE, AA>
     */
    public static function condLazy(
        bool $condition,
        callable $valid,
        callable $invalid,
    ): Validated
    {
        return $condition
            ? self::valid($valid())
            : self::invalid($invalid());
    }


    /**
     * @psalm-template B
     * @psalm-param callable(A): B $ifValid
     * @psalm-param callable(E): B $ifInvalid
     * @psalm-return B
     */
    public function fold(callable $ifValid, callable $ifInvalid): mixed
    {
        if ($this->isValid()) {
            return $ifValid($this->value);
        }

        /**
         * @var Invalid<E> $this
         */

        return $ifInvalid($this->value);
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

        /** @var Invalid<E> $this */

        return new Left($this->value);
    }

    /**
     * @psalm-return Option<A>
     */
    public function toOption(): Option
    {
        return $this->isValid()
            ? new Some($this->value)
            : None::getInstance();
    }
}
