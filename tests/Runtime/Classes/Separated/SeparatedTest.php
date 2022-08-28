<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Separated;

use Fp\Collections\ArrayList;
use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use PHPUnit\Framework\TestCase;

final class SeparatedTest extends TestCase
{
    public function testToTuple(): void
    {
        $this->assertEquals([1, 2], (Separated::create(1, 2))->toTuple());
    }
    public function testToEither(): void
    {
        $this->assertEquals(
            Either::right(ArrayList::collect([1, 2, 3])),
            Separated::create(ArrayList::collect([]), ArrayList::collect([1, 2, 3]))->toEither(),
        );

        $this->assertEquals(
            Either::left(ArrayList::collect([4, 5, 6])),
            Separated::create(ArrayList::collect([4, 5, 6]), ArrayList::collect([1, 2, 3]))->toEither(),
        );
    }

    public function testToString(): void
    {
        $this->assertEquals('Separated(1, 2)', Separated::create(1, 2)->toString());
    }
}
