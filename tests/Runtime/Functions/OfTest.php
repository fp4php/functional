<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\SubBar;

use function Fp\of;

final class OfTest extends TestCase
{
    public function testOf(): void
    {
        $this->assertTrue(of(Bar::class, Bar::class, false));
        $this->assertTrue(of(Bar::class, Bar::class, true));
        $this->assertTrue(of(SubBar::class, Bar::class, false));
        $this->assertFalse(of(SubBar::class, Bar::class, true));

        $this->assertTrue(of(new Bar(1), Bar::class, false));
        $this->assertTrue(of(new Bar(1), Bar::class, true));
        $this->assertTrue(of(new SubBar(1), Bar::class, false));
        $this->assertFalse(of(new SubBar(1), Bar::class, true));

        $this->assertFalse(of(1, Bar::class, true));
    }
}
