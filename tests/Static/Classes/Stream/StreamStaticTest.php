<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Stream;

use Fp\Streams\Stream;

final class StreamStaticTest
{
    /**
     * @psalm-param int $input
     * @psalm-return Stream<int>
     */
    public function testEmit(mixed $input): Stream
    {
        return Stream::emit($input);
    }

    /**
     * @psalm-param array{1, 2, 'a'} $input
     * @psalm-return Stream<1|2|'a'>
     */
    public function testEmits(mixed $input): Stream
    {
        return Stream::emits($input);
    }

    /**
     * @psalm-param array<int, string> $input
     * @psalm-return Stream<array{int, string}>
     */
    public function testEmitsPairs(mixed $input): Stream
    {
        return Stream::emitsPairs($input);
    }
}
