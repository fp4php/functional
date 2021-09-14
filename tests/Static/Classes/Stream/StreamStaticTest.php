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
    public function testEmit(mixed $input): mixed
    {
        return Stream::emit($input);
    }

    /**
     * @psalm-param array{1, 2, 'a'} $input
     * @psalm-return Stream<1|2|'a'>
     */
    public function testEmits(mixed $input): mixed
    {
        return Stream::emits($input);
    }
}
