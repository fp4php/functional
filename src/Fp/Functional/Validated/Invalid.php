<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template-covariant E
 * @template-covariant A
 * @psalm-immutable
 * @extends Validated<E, A>
 */
final class Invalid extends Validated
{
    /**
     * @psalm-param E $value
     */
    public function __construct(protected int|float|bool|string|object|array $value) {}

    /**
     * @template EE
     * @template AA
     * @psalm-param EE $value
     * @psalm-return self<EE, AA>
     */
    public static function of(int|float|bool|string|object|array $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return E
     */
    public function get(): int|float|bool|string|object|array
    {
        return $this->value;
    }
}
