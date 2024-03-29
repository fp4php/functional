<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Collections\ArrayList;
use Fp\Collections\Collection;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Foo;

use function Fp\Evidence\classStringOf;
use function Fp\Evidence\proveCallableString;
use function Fp\Evidence\proveClassString;
use function Fp\Evidence\proveClassStringOf;
use function Fp\Evidence\proveNonEmptyString;
use function Fp\Evidence\proveString;

final class ProveStringTest extends TestCase
{
    public function testProveString(): void
    {
        $this->assertInstanceOf(None::class, proveString(true));
        $this->assertInstanceOf(None::class, proveString(false));

        $this->assertInstanceOf(None::class, proveString(1));
        $this->assertInstanceOf(None::class, proveString(0));

        $this->assertInstanceOf(None::class, proveString(1.1));
        $this->assertInstanceOf(None::class, proveString(0.0));

        $this->assertInstanceOf(Some::class, proveString('string'));
    }

    public function testProveClassString(): void
    {
        $this->assertInstanceOf(None::class, proveClassString('string'));
        $this->assertInstanceOf(None::class, proveClassString('array_map'));
        $this->assertInstanceOf(Some::class, proveClassString(Foo::class));
    }

    public function testProveCallableString(): void
    {
        $this->assertInstanceOf(None::class, proveCallableString('string'));
        $this->assertInstanceOf(Some::class, proveCallableString('array_map'));
    }

    public function testProveNonEmptyString(): void
    {
        $this->assertInstanceOf(Some::class, proveNonEmptyString('string'));
        $this->assertInstanceOf(None::class, proveNonEmptyString(''));
    }

    public function testProveClassStringOf(): void
    {
        $this->assertInstanceOf(Some::class, proveClassStringOf(ArrayList::class, Collection::class));
        $this->assertInstanceOf(None::class, proveClassStringOf(Option::class, Collection::class));

        $this->assertInstanceOf(Some::class, classStringOf(Collection::class)(ArrayList::class));
        $this->assertInstanceOf(None::class, classStringOf(Collection::class)(Option::class));
    }
}
