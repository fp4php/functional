<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes;

use Fp\Collections\ArrayList;
use PHPUnit\Framework\TestCase;

final class ExtensionTest extends TestCase
{
    protected function tearDown(): void
    {
        ArrayList::removeAllInstanceExtensions();
        ArrayList::removeAllStaticExtensions();
    }

    public function testAddInstanceMethod(): void
    {
        ArrayList::addInstanceExtension('sumAll', function(ArrayList $list) {
            /** @var ArrayList<int> $list */;
            return $list->fold(0)(fn($acc, $cur) => $acc + $cur);
        });

        /** @psalm-suppress MixedAssignment, UndefinedMagicMethod */
        $actual = ArrayList::collect([1, 2, 3])->sumAll();

        $this->assertEquals(6, $actual);
        $this->assertArrayHasKey('sumAll', ArrayList::getAllInstanceExtensions());
    }

    public function testAddInstanceMethodTwice(): void
    {
        $this->expectErrorMessage("Instance extension method 'sumAll' is already defined!");

        $function = function(ArrayList $list): int {
            /** @var ArrayList<int> $list */;
            return $list->fold(0)(fn($acc, $cur) => $acc + $cur);
        };
        ArrayList::addInstanceExtension('sumAll', $function);
        ArrayList::addInstanceExtension('sumAll', $function);
    }

    public function testAddStaticMethod(): void
    {
        ArrayList::addStaticExtension('collectChars', function(string $string) {
            return ArrayList::collect(str_split($string));
        });

        /** @psalm-suppress MixedAssignment */
        $actual = ArrayList::collectChars('abc');

        $this->assertEquals(ArrayList::collect(['a', 'b', 'c']), $actual);
        $this->assertArrayHasKey('collectChars', ArrayList::getAllStaticExtensions());
    }

    public function testAddStaticMethodTwice(): void
    {
        $this->expectErrorMessage("Static extension method 'collectChars' is already defined!");

        $function = function(string $string): ArrayList {
            return ArrayList::collect(str_split($string));
        };

        ArrayList::addStaticExtension('collectChars', $function);
        ArrayList::addStaticExtension('collectChars', $function);
    }
}
