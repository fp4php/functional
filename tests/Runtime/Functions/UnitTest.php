<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions;

use Fp\Functional\Unit;
use PHPUnit\Framework\TestCase;

use function Fp\unit;

final class UnitTest extends TestCase
{
    public function testUnit(): void
    {
        $this->assertInstanceOf(Unit::class, unit());
    }
}
