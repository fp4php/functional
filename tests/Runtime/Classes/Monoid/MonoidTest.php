<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Monoid;

use Fp\Functional\Monoid\ArrayMonoid;
use Fp\Functional\Monoid\ListMonoid;
use Fp\Functional\Monoid\Monoid;
use Fp\Functional\Semigroup\Semigroup;
use PHPUnit\Framework\TestCase;

final class MonoidTest extends TestCase
{
    public function testArrayMonoid(): void
    {
        $monoid = new ArrayMonoid();

        $this->assertEquals(
            $monoid->empty(),
            $monoid->combine(
                $monoid->empty(),
                $monoid->empty()
            )
        );

        $this->assertEquals(
            ['a' => 1],
            $monoid->combine(
                ['a' => 1],
                $monoid->empty()
            )
        );

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            $monoid->combine(['a' => 1], ['b' => 2])
        );
    }

    public function testListMonoid(): void
    {
        $monoid = new ListMonoid();

        $this->assertEquals(
            $monoid->empty(),
            $monoid->combine(
                $monoid->empty(),
                $monoid->empty()
            )
        );

        $this->assertEquals(
            [1],
            $monoid->combine(
                [1],
                $monoid->empty()
            )
        );

        $this->assertEquals(
            [1, 2],
            $monoid->combine([1], [2])
        );
    }
}
