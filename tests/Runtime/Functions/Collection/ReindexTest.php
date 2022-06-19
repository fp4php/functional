<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\reindex;
use function Fp\Collection\reindexWithKey;

final class ReindexTest extends TestCase
{
    public function testReindex(): void
    {
        $this->assertEquals(
            [1 => 1, 2 => 2],
            reindex(
                [1, 'a' => 2],
                fn (int $v) => $v
            )
        );

        $this->assertEquals(
            [0 => 1, '2' => 2],
            reindexWithKey(
                [1, '2' => 2],
                fn (int|string $k, int $v) => $k
            )
        );
    }
}
