<?php

declare(strict_types=1);

namespace Tests\Static\Callable;

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

        $this->assertBlockType($phpBlock, 'callable(int): string');
    }

    public function testCompose3(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            $aToB = fn(int $a): bool => true;
            $bToC = fn(bool $b): string => (string) $b;
            $cTod = fn(string $c): float => (float) $c;
            $result = \Fp\Callable\compose($aToB, $bToC, $cTod);
        ';

        $this->assertBlockType($phpBlock, 'callable(int): float');
    }
}
