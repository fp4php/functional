<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\zip;

final class ZipTest extends TestCase
{
    public function testZip(): void
    {
        $this->assertEquals(
            [[1, 5], [2, 4]],
            zip(
                [1, 2],
                [5, 4]
            )
        );

        $this->assertEquals(
            [[1, 5], [2, 4]],
            zip(
                [1, 2, 3],
                [5, 4]
            )
        );

        $this->assertEquals(
            [[1, 5], [2, 4]],
            zip(
                [1, 2],
                [5, 4, 3]
            )
        );

        $this->assertEquals(
            [],
            zip(
                [1, 2],
                []
            )
        );
    }
}
