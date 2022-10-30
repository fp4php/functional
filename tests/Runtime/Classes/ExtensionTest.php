<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Streams\Stream;
use Fp\Collections\ArrayListExtensions;
use Fp\Collections\HashMapExtensions;
use Fp\Collections\HashSetExtensions;
use Fp\Collections\LinkedListExtensions;
use Fp\Collections\NonEmptyArrayListExtensions;
use Fp\Collections\NonEmptyHashMapExtensions;
use Fp\Collections\NonEmptyHashSetExtensions;
use Fp\Collections\NonEmptyLinkedListExtensions;
use Fp\Functional\Either\EitherExtensions;
use Fp\Functional\Option\OptionExtensions;
use Fp\Functional\Separated\SeparatedExtensions;
use Fp\Streams\StreamExtensions;

use PHPUnit\Framework\TestCase;

final class ExtensionTest extends TestCase
{
    /** @var non-empty-string */
    private string $testMethodName = 'testMethod';

    /** @var non-empty-string  */
    private string $testStaticMethodName = 'testStaticMethod';

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function setUp(): void
    {
        $extensions = [
            ArrayListExtensions::class,
            LinkedListExtensions::class,
            HashSetExtensions::class,
            HashMapExtensions::class,
            NonEmptyArrayListExtensions::class,
            NonEmptyLinkedListExtensions::class,
            NonEmptyHashSetExtensions::class,
            NonEmptyHashMapExtensions::class,
            StreamExtensions::class,
            OptionExtensions::class,
            EitherExtensions::class,
            SeparatedExtensions::class,
        ];

        foreach ($extensions as $extension) {
            $extension::addInstanceExtension($this->testMethodName, fn() => 42);
            $extension::addStaticExtension($this->testStaticMethodName, fn() => 42);
        }
    }

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    protected function tearDown(): void
    {
        $extensions = [
            ArrayListExtensions::class,
            LinkedListExtensions::class,
            HashSetExtensions::class,
            HashMapExtensions::class,
            NonEmptyArrayListExtensions::class,
            NonEmptyLinkedListExtensions::class,
            NonEmptyHashSetExtensions::class,
            NonEmptyHashMapExtensions::class,
            StreamExtensions::class,
            OptionExtensions::class,
            EitherExtensions::class,
            SeparatedExtensions::class,
        ];

        foreach ($extensions as $extension) {
            $extension::removeInstanceExtension($this->testMethodName);
            $extension::removeStaticExtension($this->testStaticMethodName);
        }
    }

    public function providedExtensibleClasses(): array
    {
        return [
            [ArrayList::singleton(1), ArrayListExtensions::class],
            [LinkedList::singleton(1), LinkedListExtensions::class],
            [HashSet::singleton(1), HashSetExtensions::class],
            [HashMap::collect([1]), HashMapExtensions::class],
            [NonEmptyArrayList::singleton(1), NonEmptyArrayListExtensions::class],
            [NonEmptyLinkedList::singleton(1), NonEmptyLinkedListExtensions::class],
            [NonEmptyHashSet::singleton(1), NonEmptyHashSetExtensions::class],
            [NonEmptyHashMap::collectNonEmpty([1]), NonEmptyHashMapExtensions::class],
            [Stream::emit(1), StreamExtensions::class],
            [Option::some(1), OptionExtensions::class],
            [Either::right(1), EitherExtensions::class],
            [Separated::create(1, 2), SeparatedExtensions::class],
        ];
    }

    /**
     * @dataProvider providedExtensibleClasses
     * @noinspection PhpUndefinedMethodInspection
     * @psalm-suppress all
     */
    public function testCallInstanceExtensionMethod(object $instance, string $extClass): void
    {
        $actual = $instance->{$this->testMethodName}();

        $this->assertEquals(42, $actual);
        $this->assertArrayHasKey($this->testMethodName, $extClass::getAllInstanceExtensions());
    }

    /**
     * @dataProvider providedExtensibleClasses
     * @noinspection PhpUndefinedMethodInspection
     * @psalm-suppress all
     */
    public function testCallStaticExtensionMethod(object $instance, string $extClass): void
    {
        $actual = ($instance::class)::{$this->testStaticMethodName}();

        $this->assertEquals(42, $actual);
        $this->assertArrayHasKey($this->testStaticMethodName, $extClass::getAllStaticExtensions());
    }

    public function testAddInstanceMethodTwice(): void
    {
        $this->expectErrorMessage("Instance extension method '{$this->testMethodName}' is already defined!");

        ArrayListExtensions::addInstanceExtension($this->testMethodName, function(ArrayList $list): int {
            /** @var ArrayList<int> $list */;
            return $list->fold(0)(fn($acc, $cur) => $acc + $cur);
        });
    }

    public function testAddStaticMethodTwice(): void
    {
        $this->expectErrorMessage("Static extension method '{$this->testStaticMethodName}' is already defined!");

        ArrayListExtensions::addStaticExtension($this->testStaticMethodName, function(string $string): ArrayList {
            return ArrayList::collect(str_split($string));
        });
    }
}
