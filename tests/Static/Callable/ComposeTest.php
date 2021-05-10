<?php

declare(strict_types=1);

namespace Tests\Static\Callable;

use Tests\PhpBlockTestCase;

final class ComposeTest extends PhpBlockTestCase
{
    public function testCompose2(): void
    {
        $phpBlock = <<<'PHP'
            $aToB = fn(int $a): bool => true;
            $bToC = fn(bool $b): string => (string) $b;
            $result = \Fp\Callable\compose($aToB, $bToC);
        PHP;

        $this->assertBlockType($phpBlock, 'callable(int): string');
    }

    public function testCompose3(): void
    {
        $phpBlock = <<<'PHP'
            $aToB = fn(int $a): bool => true;
            $bToC = fn(bool $b): string => (string) $b;
            $cTod = fn(string $c): float => (float) $c;
            $result = \Fp\Callable\compose($aToB, $bToC, $cTod);
        PHP;

        $this->assertBlockType($phpBlock, 'callable(int): float');
    }
}
