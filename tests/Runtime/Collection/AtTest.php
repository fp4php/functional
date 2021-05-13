<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\at;

final class AtTest extends TestCase
{
    public function testAt(): void
    {
        $this->assertTrue(at(['a' => true], 'a')->get());
    }
}
