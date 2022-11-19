<?php

declare(strict_types=1);

namespace Fp\Operations;

use function Fp\Cast\asList;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class SortedOperation extends AbstractOperation
{
    /**
     * @param callable(TV, TV): int $f
     * @return list<TV>
     */
    public function __invoke(callable $f): array
    {
        $sorted = asList($this->gen);
        usort($sorted, $f);

        return $sorted;
    }

    /**
     * @return list<TV>
     */
    public function asc(): array
    {
        return $this(fn($l, $r) => $l <=> $r);
    }

    /**
     * @return list<TV>
     */
    public function desc(): array
    {
        return $this(fn($l, $r) => $r <=> $l);
    }

    /**
     * @param callable(TV): mixed $callback
     * @return list<TV>
     */
    public function ascBy(callable $callback): array
    {
        $f =
            /**
             * @param TV $l
             * @param TV $r
             */
            fn(mixed $l, mixed $r): int => $callback($l) <=> $callback($r);

        return $this($f);
    }

    /**
     * @param callable(TV): mixed $callback
     * @return list<TV>
     */
    public function descBy(callable $callback): array
    {
        $f =
            /**
             * @param TV $l
             * @param TV $r
             */
            fn(mixed $l, mixed $r): int => $callback($r) <=> $callback($l);

        return $this($f);
    }
}
