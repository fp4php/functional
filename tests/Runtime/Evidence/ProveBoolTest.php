<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveBool;
use function Fp\Evidence\proveFalse;
use function Fp\Evidence\proveTrue;

final class ProveBoolTest extends TestCase
{
    public function testProveBool(): void
    {
        $this->assertInstanceOf(Some::class, proveBool(true));
        $this->assertInstanceOf(Some::class, proveBool(false));

        $this->assertInstanceOf(None::class, proveBool(1));
        $this->assertInstanceOf(None::class, proveBool(0));

        $this->assertInstanceOf(None::class, proveBool(1.0));
        $this->assertInstanceOf(None::class, proveBool(0.0));

        $this->assertInstanceOf(None::class, proveBool('yes'));
        $this->assertInstanceOf(None::class, proveBool('no'));
    }

    public function testProveTrue(): void
    {
        $this->assertInstanceOf(Some::class, proveTrue(true));
        $this->assertInstanceOf(None::class, proveTrue(false));
    }

    public function testProveFalse(): void
    {
        $this->assertInstanceOf(None::class, proveFalse(true));
        $this->assertInstanceOf(Some::class, proveFalse(false));
    }
}
