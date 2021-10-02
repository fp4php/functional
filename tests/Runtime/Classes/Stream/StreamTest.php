<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Stream;

use Fp\Functional\Option\Some;
use Fp\Streams\Stream;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{
    public function testDoubleDrain(): void
    {
        $stream = Stream::emits([0, 1]);
        $stream->drain();

        $this->assertNull(Option::try(fn() => $stream->drain())->get());
    }

    public function testForkDetection(): void
    {
        $stream = Stream::emits([0, 1]);
        $fork1 = $stream->map(fn($i) => $i + 1);

        $this->assertNull(Option::try(fn() => $stream->map(fn($i) => $i + 1))->get());
    }

    public function testCasts(): void
    {
        $this->assertEquals([0, 1], Stream::emits([0, 1])->toArray());
        $this->assertEquals([0, 1], Stream::emits([0, 1])->toArrayList()->toArray());
        $this->assertEquals([0, 1], Stream::emits([0, 1])->toLinkedList()->toArray());
        $this->assertEquals([0, 1], Stream::emits([0, 1, 1])->toHashSet()->toArray());
        $this->assertEquals([[0, 0], [1, 1]], Stream::emits([0, 1])->toHashMap(fn($e) => [$e, $e])->toArray());
        $this->assertInstanceOf(Some::class, Option::try(fn() => Stream::emits([0, 1])->toFile('/dev/null', false)));
        $this->assertInstanceOf(Some::class, Option::try(fn() => Stream::emits([0, 1])->toFile('/dev/null', true)));
    }
}
