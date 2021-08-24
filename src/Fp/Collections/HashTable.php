<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @internal
 * @template TK
 * @template TV
 * @psalm-type hash = string
 */
final class HashTable
{
    /**
     * @var array<hash, list<array{TK, TV}>>
     */
    public array $table = [];
}
