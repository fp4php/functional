<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Reflection;

use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Reflection\getReflectionClass;
use function Fp\Reflection\getReflectionProperty;

final class ReflectionTest extends TestCase
{
    public function testGetReflectionProperty(): void
    {
        $this->assertInstanceOf(Right::class, getReflectionProperty(Foo::class, 'a'));
        $this->assertInstanceOf(Left::class, getReflectionProperty(Foo::class, 'none'));
    }

    public function testGetReflectionClass(): void
    {
        $this->assertInstanceOf(Right::class, getReflectionClass(Foo::class));
    }
}
