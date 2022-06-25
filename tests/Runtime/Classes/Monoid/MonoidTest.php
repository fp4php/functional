<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Monoid;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Functional\Monoid\ArrayListMonoid;
use Fp\Functional\Monoid\ArrayMonoid;
use Fp\Functional\Monoid\HashMapMonoid;
use Fp\Functional\Monoid\HashSetMonoid;
use Fp\Functional\Monoid\LinkedListMonoid;
use Fp\Functional\Monoid\ListMonoid;
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

    public function testLinkedListMonoid(): void
    {
        $monoid = new LinkedListMonoid();

        $this->assertEquals($monoid->empty(), $monoid->combine(
            $monoid->empty(),
            $monoid->empty()
        ));

        $this->assertEquals(
            [1],
            $monoid->combine(LinkedList::collect([1]), $monoid->empty())->toList()
        );

        $this->assertEquals(
            [1, 2],
            $monoid->combine(LinkedList::collect([1]), LinkedList::collect([2]))->toList()
        );
    }

    public function testArrayListMonoid(): void
    {
        $monoid = new ArrayListMonoid();

        $this->assertEquals($monoid->empty(), $monoid->combine(
            $monoid->empty(),
            $monoid->empty()
        ));

        $this->assertEquals(
            [1],
            $monoid->combine(ArrayList::collect([1]), $monoid->empty())->toList()
        );

        $this->assertEquals(
            [1, 2],
            $monoid->combine(ArrayList::collect([1]), ArrayList::collect([2]))->toList()
        );
    }

    public function testHashSetMonoid(): void
    {
        $monoid = new HashSetMonoid();

        $this->assertEquals($monoid->empty(), $monoid->combine(
            $monoid->empty(),
            $monoid->empty()
        ));

        $this->assertEquals(
            [1],
            $monoid->combine(HashSet::collect([1]), $monoid->empty())->toList()
        );

        $this->assertEquals(
            [1, 2],
            $monoid->combine(HashSet::collect([1]), HashSet::collect([2]))->toList()
        );
    }

    public function testHashMapMonoid(): void
    {
        $monoid = new HashMapMonoid();

        $this->assertEquals($monoid->empty(), $monoid->combine(
            $monoid->empty(),
            $monoid->empty()
        ));

        $this->assertEquals(
            [['a', 1]],
            $monoid->combine(HashMap::collectPairs([['a', 1]]), $monoid->empty())->toList()
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            $monoid->combine(HashMap::collectPairs([['a', 1]]), HashMap::collectPairs([['b', 2]]))->toList()
        );
    }
}
