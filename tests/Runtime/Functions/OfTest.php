<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions;

use Fp\Collections\Collection;
use Fp\Collections\Seq;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\SubBar;

use function Fp\classOf;
use function Fp\objectOf;
use function Fp\of;

final class OfTest extends TestCase
{
    public function testOf(): void
    {
        $this->assertTrue(classOf(Some::class, Option::class, false));
        $this->assertTrue(classOf(Seq::class, Collection::class, false));
        $this->assertTrue(classOf(Bar::class, Bar::class, false));
        $this->assertTrue(classOf(Bar::class, Bar::class, true));
        $this->assertTrue(classOf(SubBar::class, Bar::class, false));
        $this->assertFalse(classOf(SubBar::class, Bar::class, true));
        $this->assertFalse(classOf(1, Bar::class, true));

        $this->assertTrue(objectOf(new Bar(1), Bar::class, false));
        $this->assertTrue(objectOf(new Bar(1), Bar::class, true));
        $this->assertTrue(objectOf(new SubBar(1), Bar::class, false));
        $this->assertFalse(objectOf(new SubBar(1), Bar::class, true));
        $this->assertFalse(objectOf(1, Bar::class, true));

        $this->assertTrue(of(new Bar(1), Bar::class, false));
        $this->assertTrue(of(new Bar(1), Bar::class, true));
        $this->assertTrue(of(new SubBar(1), Bar::class, false));
        $this->assertFalse(of(new SubBar(1), Bar::class, true));
        $this->assertFalse(of(1, Bar::class, true));
    }
}
