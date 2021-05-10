<?php

declare(strict_types=1);

namespace Tests\Static\Callable;

use Tests\PhpBlockTestCase;

final class PartialTest extends PhpBlockTestCase
{
    public function testPartialLeftForClosure3(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a, string $b, bool $c): bool => true;
            $result = \Fp\Callable\partialLeft($callback, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(string, bool): true');
    }

    public function testPartialLeftForClosure2(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a, string $b): bool => true;
            $result = \Fp\Callable\partialLeft($callback, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(string): true');
    }

    public function testPartialLeftForClosure1(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a): bool => true;
            $result = \Fp\Callable\partialLeft($callback, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(): true');
    }

    public function testPartialRightForClosure3(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a, string $b, bool $c): bool => true;
            $result = \Fp\Callable\partialRight($callback, true);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(int, string): true');
    }

    public function testPartialRightForClosure2(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a, string $b) => true;
            $result = \Fp\Callable\partialRight($callback, '');
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(int): true');
    }

    public function testPartialRightForClosure1(): void
    {
        $phpBlock = <<<'PHP'
            $callback = fn(int $a): bool => true;
            $result = \Fp\Callable\partialRight($callback, 1);
        PHP;

        $this->assertBlockType($phpBlock, 'pure-Closure(): true');
    }
}
