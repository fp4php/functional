<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Validated;

use Fp\Functional\Validated\Validated;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\map;

final class ValidatedTest extends TestCase
{
    public function testCombine(): void
    {
        $valid = Validated::valid(1)
            ->combine(Validated::valid(2))
            ->combine(Validated::valid(3));

        $invalid = $valid
            ->combine(Validated::invalid('err1'))
            ->combine(Validated::invalid('err2'));

        $this->assertEquals([1, 2, 3], $valid->get());
        $this->assertEquals(['err1', 'err2'], $invalid->get());
    }

    public function testWithList(): void
    {
        function validateEven(int $i): Validated {
            return $i % 2 === 0
                ? Validated::valid($i)
                : Validated::invalid("$i is not even");
        }

        /**
         * @psalm-param non-empty-list<int> $list
         */
        function validateEvenList(array $list): Validated {
            $elementsToValidate = map($list, fn(int $i) => validateEven($i));
            $validated = array_shift($elementsToValidate);

            foreach ($elementsToValidate as $v) {
                $validated = $validated->combine($v);
            }

            return $validated;
        }

        $this->assertEquals([2, 4, 6], validateEvenList([2, 4, 6])->get());
        $this->assertEquals(['3 is not even', '5 is not even'], validateEvenList([3, 5, 6])->get());
    }
}
