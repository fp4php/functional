<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Generator;
use Throwable;

/**
 * @template-covariant E
 * @template-covariant A
 * @psalm-yield A
 * @psalm-immutable
 */
abstract class Validated
{
    /**
     * @psalm-template B
     * @psalm-param \Closure(A): B $closure
     * @psalm-return Validated<E, B>
     */
    public function map(\Closure $closure): Validated
    {
        if ($this->isInvalid()) {
            return new Invalid($this->value);
        }

        /**
         * @var Valid<E, A> $this
         */

        return new Valid($closure($this->value));
    }

    /**
     * @psalm-template B
     * @psalm-param \Closure(A): Validated<E, B> $closure
     * @psalm-return Validated<E, B>
     */
    public function flatMap(\Closure $closure): Validated
    {
        if ($this->isInvalid()) {
            return new Invalid($this->value);
        }

        /**
         * @var Valid<E, A> $this
         */

        return $closure($this->value);
    }

    /**
     * @psalm-assert-if-true Valid<E, A> $this
     */
    public function isValid(): bool
    {
        return $this instanceof Valid;
    }

    /**
     * @psalm-assert-if-true Invalid<E, A> $this
     */
    public function isInvalid(): bool
    {
        return $this instanceof Invalid;
    }

    /**
     * @psalm-template EE
     * @psalm-param EE $value
     * @psalm-return Invalid<EE, empty>
     */
    public static function invalid(int|float|bool|string|object|array $value): Invalid
    {
        return Invalid::of($value);
    }

    /**
     * @psalm-template AA
     * @psalm-param AA $value
     * @psalm-return Valid<empty, AA>
     */
    public static function valid(int|float|bool|string|object|array $value): Valid
    {
        return Valid::of($value);
    }

    /**
     * @psalm-return E|A
     */
    abstract public function get(): int|float|bool|string|object|array;
}
