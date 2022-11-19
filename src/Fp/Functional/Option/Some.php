<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @template-covariant A
 * @extends Option<A>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class Some extends Option
{
    /**
     * @param A $value
     */
    public function __construct(
        private readonly mixed $value,
    ) {}

    /**
     * {@inheritDoc}
     *
     * @template SO
     * @template NO
     *
     * @param callable(): NO $ifNone
     * @param callable(A): SO $ifSome
     * @return SO|NO
     */
    public function fold(callable $ifNone, callable $ifSome): mixed
    {
        return $ifSome($this->value);
    }
}
