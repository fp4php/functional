<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Either\Either;
use PHPUnit\Framework\TestCase;
use function Fp\Collection\sequenceEither;
use function Fp\Collection\traverseEither;

final class EitherTraverseTest extends TestCase
{
    public function testTraverse(): void
    {
        /** @psalm-var list<int> $c */
        $c = [1, 2];

        $this->assertEquals(
            Either::right($c),
            traverseEither($c, fn(int $v) => $v < 3
                ? Either::right($v)
                : Either::left('Is too high'))
        );

        $this->assertEquals(
            Either::left('Is too high'),
            traverseEither($c, fn(int $v) => $v < 2
                ? Either::right($v)
                : Either::left('Is too high'))
        );
    }

    public function testSequence(): void
    {
        $this->assertEquals(
            Either::right([1, 2]),
            sequenceEither([
                Either::right(1),
                Either::right(2),
            ])
        );

        $this->assertEquals(
            Either::left('error'),
            sequenceEither([
                Either::right(1),
                Either::left('error'),
            ])
        );

        $this->assertEquals(
            Either::left('error'),
            sequenceEither([
                Either::left('error'),
                Either::right(1),
            ])
        );
    }
}
