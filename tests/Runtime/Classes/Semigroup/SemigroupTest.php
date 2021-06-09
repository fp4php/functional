<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Semigroup;

use Fp\Functional\Semigroup\NonEmptyArraySemigroup;
use Fp\Functional\Semigroup\Semigroup;
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
        $semigroup = Semigroup::nonEmptyListInstance('int');
        $this->assertEquals(
            [1, 2],
            $semigroup->combine([1], [2])
        );
    }

    public function testListSemigroup(): void
    {
        $semigroup = Semigroup::listInstance('int');
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
        $semigroup = Semigroup::lhsInstance('int');
        $this->assertEquals(
            1,
            $semigroup->combine(1, 2)
        );
    }

    public function testRhsSemigroup(): void
    {
        $semigroup = Semigroup::rhsInstance('int');
        $this->assertEquals(
            2,
            $semigroup->combine(1, 2)
        );
    }

    public function testValidatedSemigroup(): void
    {
        $validInstance = Semigroup::listInstance('int');
        $invalidInstance = Semigroup::listInstance('string');

        $semigroup = Semigroup::validatedInstance(
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
