<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\copyCollection;

final class CopyCollectionTest extends TestCase
{
    public function testCopyCollection(): void
    {
        $c = ['a' => 1, 'b' => 2];
        $this->assertEquals($c, copyCollection($c));
    }
}
