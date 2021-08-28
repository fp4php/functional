<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * Public API for Map key-value pair
 * Exists only inside one iteration cycle
 * And destroyed after iteration
 *
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 */
class Entry
{
    /**
     * @param TK $key
     * @param TV $value
     */
    public function __construct(public mixed $key, public mixed $value)
    {
    }

    /**
     * @return array{TK, TV}
     */
    public function toArray(): array
    {
        return [$this->key, $this->value];
    }
}
