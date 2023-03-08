<?php

declare(strict_types=1);

namespace Tests\Mock;

use Iterator;

/**
 * @internal
 * @implements Iterator<int, int>
 */
class FooIterable implements Iterator
{
    public function current(): int
    {
        return 0;
    }

    public function next(): void
    {
    }

    public function key(): int
    {
        return 0;
    }

    public function valid(): bool
    {
        return false;
    }

    public function rewind(): void
    {
    }
}
