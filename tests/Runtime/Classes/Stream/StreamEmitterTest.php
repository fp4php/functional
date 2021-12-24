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
        $this->assertEquals([0], Stream::awakeEvery(0)->take(1)->toArray());
    }

    public function testConstant(): void
    {
        $this->assertEquals([1, 1], Stream::constant(1)->take(2)->toArray());
    }

    public function testInfinite(): void
    {
        $this->assertEquals([unit(), unit()], Stream::infinite()->take(2)->toArray());
    }

    public function testRange(): void
    {
        $this->assertEquals([0, 1], Stream::range(0, 2)->toArray());
        $this->assertEquals([0, 2, 4], Stream::range(0, 5, 2)->toArray());
    }

    public function testEmitsPairs(): void
    {
        $this->assertEquals([['a', 1]], Stream::emitsPairs(['a' => 1])->toArray());
    }
}
