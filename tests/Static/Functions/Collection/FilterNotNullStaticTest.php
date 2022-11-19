<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use function Fp\Collection\filterNotNull;

final class FilterNotNullStaticTest
{
    /**
     * @param list<int|null> $list
     * @return list<int>
     */
    public function withList(array $list): array
    {
        return filterNotNull($list);
    }

    /**
     * @param array<string, int|null> $array
     * @return array<string, int>
     */
    public function withArray(array $array): array
    {
        return filterNotNull($array);
    }

    /**
     * @param array{1, 2, 3, null} $list
     * @return list<1|2|3>
     */
    public function withListLiterals(array $list): array
    {
        return filterNotNull($list);
    }

    /**
     * @param array{fst: int, snd: int, trd: int|null} $shape
     * @return array{fst: int, snd: int, trd?: int}
     */
    public function withShape(array $shape): array
    {
        return filterNotNull($shape);
    }
}
