<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Stream;

use Fp\Streams\Stream;
use PHPUnit\Framework\TestCase;

use function Fp\unit;

final class StreamEmitterTest extends TestCase
{
    public function testAwake(): void
    {
        $this->assertEquals([0], Stream::awakeEvery(0)->take(1)->toList());
    }

    public function testConstant(): void
    {
        $this->assertEquals([1, 1], Stream::constant(1)->take(2)->toList());
    }

    public function testInfinite(): void
    {
        $this->assertEquals([unit(), unit()], Stream::infinite()->take(2)->toList());
    }

    public function testRange(): void
    {
        $this->assertEquals([0, 1], Stream::range(0, 2)->toList());
        $this->assertEquals([0, 2, 4], Stream::range(0, 5, 2)->toList());
    }
}
