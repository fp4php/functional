<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template-covariant E
 * @template-covariant A
 * @psalm-immutable
 * @extends Validated<E, A>
 */
final class Valid extends Validated
{
    /**
     * @var non-empty-list<A>
     */
    protected array $value;

    /**
     * @psalm-param non-empty-list<A> $value
     */
    public function __construct(array $value) {
        $this->value = $value;
    }

    /**
     * @psalm-return non-empty-list<A>
     */
    public function get(): array
    {
        return $this->value;
    }
}
