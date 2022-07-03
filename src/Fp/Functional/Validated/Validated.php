<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;

/**
 * @template-covariant E
 * @template-covariant A
 *
 * @psalm-suppress InvalidTemplateParam
 */
abstract class Validated
{
    /**
     * @template EE
     *
     * @param EE $value
     * @return Validated<EE, never>
     */
    public static function invalid(mixed $value): Validated
    {
        return new Invalid($value);
    }

    /**
     * @template AA
     *
     * @param AA $value
     * @return Validated<never, AA>
     */
    public static function valid(mixed $value): Validated
    {
        return new Valid($value);
    }

    /**
     * @return E|A
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
     * @template EE
     * @template AA
     *
     * @param EE $invalid
     * @param AA $valid
     * @return Validated<EE, AA>
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
     * @template EE
     * @template AA
     *
     * @param callable(): EE $invalid
     * @param callable(): AA $valid
     * @return Validated<EE, AA>
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
     * @template B
     *
     * @param callable(A): B $ifValid
     * @param callable(E): B $ifInvalid
     * @return B
     */
    public function fold(callable $ifValid, callable $ifInvalid): mixed
    {
        return $this->isValid()
            ? $ifValid($this->get())
            : $ifInvalid($this->get());
    }

    /**
     * @return Either<E, A>
     */
    public function toEither(): Either
    {
        return $this->isValid()
            ? Either::right($this->get())
            : Either::left($this->get());
    }

    /**
     * @return Option<A>
     */
    public function toOption(): Option
    {
        return $this->isValid()
            ? Option::some($this->get())
            : Option::none();
    }
}
