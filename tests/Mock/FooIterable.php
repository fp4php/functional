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
    public function current()
    {
    }

    public function next()
    {
    }

    public function key()
    {
    }

    public function valid(): bool
    {
        return false;
    }

    public function rewind()
    {
    }
}
