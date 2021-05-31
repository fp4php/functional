<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Callable;

use Tests\PhpBlockTestCase;

final class ComposeTest extends PhpBlockTestCase
{
    public function testCompose2(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $aToB = fn(int $a): bool => true;
            $bToC = fn(bool $b): string => (string) $b;
            $result = \Fp\Callable\compose($aToB, $bToC);
        ';

        $this->assertBlockTypes($phpBlock, 'callable(int): string');
    }

    public function testCompose3(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $aToB = fn(int $a): bool => true;
            $bToC = fn(bool $b): string => (string) $b;
            $cTod = fn(string $c): float => (float) $c;
            $result = \Fp\Callable\compose($aToB, $bToC, $cTod);
        ';

        $this->assertBlockTypes($phpBlock, 'callable(int): float');
    }
}
