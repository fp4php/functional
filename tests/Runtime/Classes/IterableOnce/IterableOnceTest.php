<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\IterableOnce;

use Fp\Collections\IterableOnce;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class IterableOnceTest extends TestCase
{
    public function testIterableOnce(): void
    {
        $buffer = [];
        $iterableOnce = IterableOnce::of(fn() => [1, 2, 3]);

        foreach ($iterableOnce as $elem) {
            $buffer[] = $elem;
        }

        $this->assertEquals([1, 2, 3], $buffer);
        $this->assertNull(Option::try(function() use ($iterableOnce) {
            $buffer = [];

            foreach ($iterableOnce as $elem) {
                $buffer[] = $elem;
            }

            return $buffer;
        })->get());
    }
}
