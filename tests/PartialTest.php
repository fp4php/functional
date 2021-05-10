<?php

declare(strict_types=1);

namespace Tests;

final class PartialTest extends PhpBlockTestCase
{
    public function testPartialLeftForClosure3(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialLeft(function(int $a, string $b, bool $c): bool {}, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(string, bool): bool');
    }

    public function testPartialLeftForClosure2(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialLeft(function(int $a, string $b): bool {}, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(string): bool');
    }

    public function testPartialLeftForClosure1(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialLeft(function(int $a): bool {}, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(): bool');
    }

    public function testPartialRightForClosure3(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialRight(function(int $a, string $b, bool $c): bool {}, true);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(int, string): bool');
    }

    public function testPartialRightForClosure2(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialRight(function(int $a, string $b): bool {}, '');
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(int): bool');
    }

    public function testPartialRightForClosure1(): void
    {
        $phpBlock = <<<'PHP'
            $result = \Fp\Function\Callable\partialRight(function(int $a): bool {}, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(): bool');
    }
}
