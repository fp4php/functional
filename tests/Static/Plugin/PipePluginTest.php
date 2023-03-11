<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use function Fp\Callable\pipe;
use function Fp\Collection\filter;
use function Fp\Collection\map;

final class PipePluginTest
{
    /**
     * @return 42
     */
    public function testSimple(): int
    {
        return pipe(
            0,
            fn($i) => $i + 11,
            fn($i) => $i + 20,
            fn($i) => $i + 11,
        );
    }

    /**
     * @param list<string> $items
     * @return list<int>
     */
    public function testMap(array $items): array
    {
        return pipe(
            $items,
            fn($i) => filter($i, is_numeric(...)),
            fn($i) => map($i, fn($v) => (int) $v),
        );
    }
}
