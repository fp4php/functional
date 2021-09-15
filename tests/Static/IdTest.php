<?php

declare(strict_types=1);

namespace Tests\Static;

use function Fp\id;

final class IdTest
{
    /**
     * @psalm-return 1
     */
    public function testWithArray(): int
    {
        return id(1);
    }
}
