<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Semigroup;

use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Monoid\ListMonoid;
use Fp\Functional\Monoid\Monoid;
use Fp\Functional\Semigroup\LhsSemigroup;
use Fp\Functional\Semigroup\NonEmptyArrayListSemigroup;
use Fp\Functional\Semigroup\NonEmptyArraySemigroup;
use Fp\Functional\Semigroup\NonEmptyHashMapSemigroup;
use Fp\Functional\Semigroup\NonEmptyHashSetSemigroup;
use Fp\Functional\Semigroup\NonEmptyLinkedListSemigroup;
use Fp\Functional\Semigroup\NonEmptyListSemigroup;
use Fp\Functional\Semigroup\RhsSemigroup;
use Fp\Functional\Semigroup\Semigroup;
use Fp\Functional\Semigroup\ValidatedSemigroup;
use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;
use PHPUnit\Framework\TestCase;

final class SemigroupTest extends TestCase
{
    public function testNonEmptyArraySemigroup(): void
    {
        $semigroup = new NonEmptyArraySemigroup();
        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            $semigroup->combine(['a' => 1], ['b' => 2])
        );
    }

    public function testNonEmptyListSemigroup(): void
    {
        /** @var Semigroup<non-empty-list<int>> $semigroup */
        $semigroup = new NonEmptyListSemigroup();

        $this->assertEquals(
            [1, 2],
            $semigroup->combine([1], [2])
        );
    }

    public function testListSemigroup(): void
    {
        /** @var Monoid<list<int>> $semigroup */
        $semigroup = new ListMonoid();

        $this->assertEquals(
            [1, 2],
            $semigroup->combine([1], [2])
        );
        $this->assertEquals(
            [1],
            $semigroup->combine([1], [])
        );
    }

    public function testLhsSemigroup(): void
    {
        /** @var Semigroup<int> $semigroup */
        $semigroup = new LhsSemigroup();

        $this->assertEquals(
            1,
            $semigroup->combine(1, 2)
        );
    }

    public function testRhsSemigroup(): void
    {
        /** @var Semigroup<int> $semigroup */
        $semigroup = new RhsSemigroup();

        $this->assertEquals(
            2,
            $semigroup->combine(1, 2)
        );
    }

    public function testValidatedSemigroup(): void
    {
        /** @psalm-var Monoid<list<int>> $validInstance */
        $validInstance = new ListMonoid();

        /** @psalm-var Monoid<list<string>> $invalidInstance */
        $invalidInstance = new ListMonoid();

        $semigroup = new ValidatedSemigroup(
            $validInstance,
            $invalidInstance
        );

        $this->assertInstanceOf(
            Valid::class,
            $semigroup->combine(
                Validated::valid([1]),
                Validated::valid([2]),
            )
        );

        $this->assertEquals(
            [1, 2],
            $semigroup->combine(
                Validated::valid([1]),
                Validated::valid([2]),
            )->get()
        );

        $this->assertInstanceOf(
            Invalid::class,
            $semigroup->combine(
                Validated::invalid(['err1']),
                Validated::valid([2]),
            )
        );

        $this->assertEquals(
            ['err1'],
            $semigroup->combine(
                Validated::invalid(['err1']),
                Validated::valid([2]),
            )->get()
        );

        $this->assertInstanceOf(
            Invalid::class,
            $semigroup->combine(
                Validated::valid([2]),
                Validated::invalid(['err1']),
            )
        );

        $this->assertEquals(
            ['err1'],
            $semigroup->combine(
                Validated::valid([2]),
                Validated::invalid(['err1']),
            )->get()
        );

        $this->assertInstanceOf(
            Invalid::class,
            $semigroup->combine(
                Validated::invalid(['err1']),
                Validated::invalid(['err2']),
            )
        );

        $this->assertEquals(
            ['err1', 'err2'],
            $semigroup->combine(
                Validated::invalid(['err1']),
                Validated::invalid(['err2']),
            )->get()
        );
    }

    public function testLinkedListSemigroup(): void
    {
        /** @var Semigroup<NonEmptyLinkedList<int>> $semigroup */
        $semigroup = new NonEmptyLinkedListSemigroup();

        $this->assertEquals(
            [1, 2],
            $semigroup->combine(NonEmptyLinkedList::collectNonEmpty([1]), NonEmptyLinkedList::collectNonEmpty([2]))->toArray()
        );
    }

    public function testArrayListSemigroup(): void
    {
        /** @var Semigroup<NonEmptyArrayList<int>> $semigroup */
        $semigroup = new NonEmptyArrayListSemigroup();

        $this->assertEquals(
            [1, 2],
            $semigroup->combine(NonEmptyArrayList::collectNonEmpty([1]), NonEmptyArrayList::collectNonEmpty([2]))->toArray()
        );
    }

    public function testHashSetSemigroup(): void
    {
        /** @var Semigroup<NonEmptyHashSet<int>> $semigroup */
        $semigroup = new NonEmptyHashSetSemigroup();

        $this->assertEquals(
            [1, 2],
            $semigroup->combine(NonEmptyHashSet::collectNonEmpty([1]), NonEmptyHashSet::collectNonEmpty([2]))->toArray()
        );
    }

    public function testHashMapSemigroup(): void
    {
        /** @var Semigroup<NonEmptyHashMap<string, int>> $semigroup */
        $semigroup = new NonEmptyHashMapSemigroup();

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            $semigroup->combine(NonEmptyHashMap::collectPairsNonEmpty([['a', 1]]), NonEmptyHashMap::collectPairsNonEmpty([['b', 2]]))->toArray()
        );
    }
}
