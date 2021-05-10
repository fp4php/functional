<?php

declare(strict_types=1);

namespace Tests;

final class PluckTest extends PhpBlockTestCase
{
    public function testIntProperty(): void
    {
        $phpBlock = <<<'PHP'
            use Tests\Mock\Foo;
            use function Fp\Collection\pluck;
            
            $result = pluck([new Foo(1), new Foo(2)], 'a');
        PHP;

        $this->assertBlockType($phpBlock, 'array<array-key, int>');
    }
}
