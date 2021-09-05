<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Semigroup;

use Fp\Functional\Monoid\ListMonoid;
use Fp\Functional\Monoid\Monoid;
use Fp\Functional\Semigroup\LhsSemigroup;
use Fp\Functional\Semigroup\NonEmptyArraySemigroup;
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
}
