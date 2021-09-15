<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Callable;

use function Fp\Callable\compose;

final class ComposeStaticTest
{
    /**
     * @return callable(int): string
     */
    public function testCompose2(): callable
    {
        $aToB = fn(int $a): bool => true;
        $bToC = fn(bool $b): string => (string) $b;
        return compose($aToB, $bToC);
    }

    /**
     * @return callable(int): float
     */
    public function testCompose3(): callable
    {
        $aToB = fn(int $a): bool => true;
        $bToC = fn(bool $b): string => (string) $b;
        $cTod = fn(string $c): float => (float) $c;
        return compose($aToB, $bToC, $cTod);
    }
}
