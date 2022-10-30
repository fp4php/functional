<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes;

use Fp\Collections\ArrayList;
use Fp\Collections\ArrayListExtensions;
use PHPUnit\Framework\TestCase;

final class ExtensionTest extends TestCase
{
    /** @var non-empty-string */
    private string $testMethodName = 'testMethod';

    /** @var non-empty-string  */
    private string $testStaticMethodName = 'testStaticMethod';

    protected function setUp(): void
    {
        ArrayListExtensions::addInstanceExtension($this->testMethodName, function(ArrayList $list) {
            /** @var ArrayList<int> $list */;
            return $list->fold(0)(fn($acc, $cur) => $acc + $cur);
        });

        ArrayListExtensions::addStaticExtension($this->testStaticMethodName, function(string $string): ArrayList {
            return ArrayList::collect(str_split($string));
        });
    }

    protected function tearDown(): void
    {
        ArrayListExtensions::removeInstanceExtension($this->testMethodName);
        ArrayListExtensions::removeStaticExtension($this->testStaticMethodName);
    }

    public function testAddInstanceMethod(): void
    {
        /** @psalm-suppress MixedAssignment */
        $actual = ArrayList::collect([1, 2, 3])->{$this->testMethodName}();

        $this->assertEquals(6, $actual);
        $this->assertArrayHasKey($this->testMethodName, ArrayListExtensions::getAllInstanceExtensions());
    }

    public function testAddInstanceMethodTwice(): void
    {
        $this->expectErrorMessage("Instance extension method '{$this->testMethodName}' is already defined!");

        ArrayListExtensions::addInstanceExtension($this->testMethodName, function(ArrayList $list): int {
            /** @var ArrayList<int> $list */;
            return $list->fold(0)(fn($acc, $cur) => $acc + $cur);
        });
    }

    public function testAddStaticMethod(): void
    {
        /** @psalm-suppress MixedAssignment */
        $actual = ArrayList::{$this->testStaticMethodName}('abc');

        $this->assertArrayHasKey($this->testStaticMethodName, ArrayListExtensions::getAllStaticExtensions());
        $this->assertEquals(ArrayList::collect(['a', 'b', 'c']), $actual);
    }

    public function testAddStaticMethodTwice(): void
    {
        $this->expectErrorMessage("Static extension method '{$this->testStaticMethodName}' is already defined!");

        ArrayListExtensions::addStaticExtension($this->testStaticMethodName, function(string $string): ArrayList {
            return ArrayList::collect(str_split($string));
        });
    }
}
