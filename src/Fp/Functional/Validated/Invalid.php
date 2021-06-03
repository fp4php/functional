<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

use Fp\Functional\Semigroup\NonEmptyListSemigroup;

/**
 * @template-covariant E
 * @psalm-immutable
 * @extends Validated<E, empty>
 */
final class Invalid extends Validated
{
    /**
     * @var non-empty-list<E>
     */
    protected array $value;

    /**
     * @var NonEmptyListSemigroup<E>
     */
    protected NonEmptyListSemigroup $semi;

    /**
     * @psalm-param non-empty-list<E> $value
     */
    public function __construct(array $value) {
        $this->value = $value;
        $this->semi = new NonEmptyListSemigroup($this->value);
    }

    /**
     * @psalm-return non-empty-list<E>
     */
    public function get(): array
    {
        return $this->value;
    }
}
