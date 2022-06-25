<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Stream;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Some;
use Fp\Streams\Stream;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{
    public function testDoubleDrain(): void
    {
        $stream = Stream::emits([0, 1]);
        $stream->drain();

        $this->assertNull(Option::try(fn() => $stream->drain())->get());
    }

    public function testForkDetection(): void
    {
        $stream = Stream::emits([0, 1]);
        $fork1 = $stream->map(fn($i) => $i + 1);

        $this->assertNull(Option::try(fn() => $stream->map(fn($i) => $i + 1))->get());
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [0, 1],
            Stream::emits([0, 1])->toArray(),
        );

        $this->assertEquals(
            Option::some([0, 1]),
            Stream::emits([0, 1])->toNonEmptyArray(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyArray(),
        );

        $this->assertEquals(
            ArrayList::collect([0, 1]),
            Stream::emits([0, 1])->toArrayList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyArrayList::collectNonEmpty([0, 1])),
            Stream::emits([0, 1])->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            LinkedList::collect([0, 1]),
            Stream::emits([0, 1])->toLinkedList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyLinkedList::collectNonEmpty([0, 1])),
            Stream::emits([0, 1])->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            HashSet::collect([0, 1]),
            Stream::emits([0, 1, 1])->toHashSet(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashSet::collectNonEmpty([0, 1])),
            Stream::emits([0, 1, 1])->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            HashMap::collectPairs([[0, 0], [1, 1]]),
            Stream::emits([[0, 0], [1, 1]])->toHashMap(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashMap::collectPairsNonEmpty([[0, 0], [1, 1]])),
            Stream::emits([[0, 0], [1, 1]])->toNonEmptyHashMap(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyHashMap(),
        );

        $this->assertInstanceOf(
            Some::class,
            Option::try(fn() => Stream::emits([0, 1])->toFile('/dev/null', false)),
        );

        $this->assertInstanceOf(
            Some::class,
            Option::try(fn() => Stream::emits([0, 1])->toFile('/dev/null', true)),
        );

        $this->assertEquals(
            [1 => 'a', 2 => 'b'],
            Stream::emits([[1, 'a'], [2, 'b']])->toAssocArray()
        );

        $this->assertEquals(
            Option::some([1 => 'a', 2 => 'b']),
            Stream::emits([[1, 'a'], [2, 'b']])->toNonEmptyAssocArray(),
        );

        $this->assertEquals(
            Option::none(),
            Stream::emits([])->toNonEmptyAssocArray(),
        );
    }
}
