<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\chunks;

final class ChunksTest extends TestCase
{
    public function testWithArray(): void
    {
        $this->assertEquals(
            [[1, 2], [3, 4]],
            iterator_to_array(chunks([1, 2, 3, 4], 2))
        );

        $this->assertEquals(
            [[1], [2], [3], [4]],
            iterator_to_array(chunks([1, 2, 3, 4], 1))
        );
    }

    public function testWithPartialLastChunk(): void
    {
        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            iterator_to_array(chunks([1, 2, 3, 4, 5], 2))
        );
    }

    public function testWithEmptyArray(): void
    {
        $this->assertEquals(
            [],
            iterator_to_array(chunks([], 2))
        );
    }
}
