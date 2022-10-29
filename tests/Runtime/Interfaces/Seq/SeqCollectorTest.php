<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\Nil;
use Fp\Collections\Seq;
use PHPUnit\Framework\TestCase;

final class SeqCollectorTest extends TestCase
{
    /**
     * @return list<array{class-string<Seq>}>
     */
    public function seqClassDataProvider(): array
    {
        return [
            [ArrayList::class],
            [LinkedList::class],
        ];
    }

    public function testNilIsSingleton(): void
    {
        $this->assertTrue(Nil::getInstance() === Nil::getInstance());
    }

    /**
     * @param class-string<Seq> $seq
     * @dataProvider seqClassDataProvider
     */
    public function testCollect(string $seq): void
    {
        $this->assertEquals([1, 2, 3], $seq::collect([1, 2, 3])->toList());
    }

    /**
     * @param class-string<Seq> $seq
     * @dataProvider seqClassDataProvider
     */
    public function testSingleton(string $seq): void
    {
        $this->assertEquals([1], $seq::singleton(1)->toList());
    }

    /**
     * @param class-string<Seq> $seq
     * @dataProvider seqClassDataProvider
     */
    public function testEmpty(string $seq): void
    {
        $this->assertEquals([], $seq::empty()->toList());
    }

    /**
     * @param class-string<Seq> $seq
     * @dataProvider seqClassDataProvider
     */
    public function testRange(string $seq): void
    {
        $this->assertEquals([], $seq::range(0, 0)->toList());
        $this->assertEquals([0, 1, 2], $seq::range(0, 3)->toList());
        $this->assertEquals([0, 2], $seq::range(0, 3, 2)->toList());
    }
}
