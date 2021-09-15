<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\keys;

final class KeysStaticTest
{
    /**
     * @psalm-param array{
     *     array<string, int>,
     *     non-empty-array<string, int>,
     *     array<int, int>,
     *     non-empty-array<int, int>,
     *     array<int|string, int>,
     *     non-empty-array<int|string, int>
     * } $in
     * @psalm-return array{
     *     list<string>,
     *     non-empty-list<string>,
     *     list<int>,
     *     non-empty-list<int>,
     *     list<int|string>,
     *     non-empty-list<int|string>,
     * }
     */
    public function testArrayToKeys(array $in): array
    {
        return [
            keys($in[0]),
            keys($in[1]),
            keys($in[2]),
            keys($in[3]),
            keys($in[4]),
            keys($in[5]),
        ];
    }
}
