<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Either\Either;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\sequenceEither;
use function Fp\Collection\sequenceEitherAcc;
use function Fp\Collection\traverseEither;
use function Fp\Collection\traverseEitherAcc;
use function Fp\Collection\traverseEitherKV;

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

    public function testTraverseAcc(): void
    {
        /** @psalm-var list<int> $c */
        $c = [1, 2, 3];

        $this->assertEquals(
            Either::right($c),
            traverseEitherAcc($c, fn(int $v) => $v <= 3
                ? Either::right($v)
                : Either::left('Is too high'))
        );

        $this->assertEquals(
            Either::left(['2 is too high', '3 is too high']),
            traverseEitherAcc($c, fn(int $v) => $v < 2
                ? Either::right($v)
                : Either::left("{$v} is too high"))
        );
    }

    public function testTraverseKV(): void
    {
        $c1 = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        $this->assertEquals(
            Either::right($c1),
            traverseEitherKV($c1, fn(int $k, int $v) => $k === $v
                ? Either::right($v)
                : Either::left('err')),
        );

        $keysBanList = ['fst'];
        $valuesBanList = [3, 4];
        $c2 = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
            'fth' => 4,
        ];

        $this->assertEquals(
            Either::left('ban'),
            traverseEitherKV($c2, fn(string $k, int $v) => in_array($k, $keysBanList) || in_array($v, $valuesBanList)
                ? Either::left('ban')
                : Either::right($v))
        );
    }

    public function testSequence(): void
    {
        $this->assertEquals(
            Either::right([]),
            sequenceEither([]),
        );

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

    public function testLazySequence(): void
    {
        $this->assertEquals(
            Either::right([1, 2]),
            sequenceEither([
                fn() => Either::right(1),
                fn() => Either::right(2),
            ])
        );

        $this->assertEquals(
            Either::left('error'),
            sequenceEither([
                fn() => Either::right(1),
                fn() => Either::left('error'),
            ])
        );

        $this->assertEquals(
            Either::left('error'),
            sequenceEither([
                fn() => Either::left('error'),
                fn() => Either::right(1),
            ])
        );
    }

    public function testSequenceAcc(): void
    {
        $this->assertEquals(
            Either::left([
                'err1',
                'err2',
            ]),
            sequenceEitherAcc([
                Either::left('err1'),
                Either::right('val'),
                Either::left('err2'),
            ]),
        );

        $this->assertEquals(
            Either::left([
                'k1' => 'err1',
                'k3' => 'err2',
            ]),
            sequenceEitherAcc([
                'k1' => Either::left('err1'),
                'k2' => Either::right('val'),
                'k3' => Either::left('err2'),
            ]),
        );
    }

    public function testLazySequenceAcc(): void
    {
        $this->assertEquals(
            Either::left([
                'err1',
                'err2',
            ]),
            sequenceEitherAcc([
                fn() => Either::left('err1'),
                fn() => Either::right('val'),
                fn() => Either::left('err2'),
            ]),
        );

        $this->assertEquals(
            Either::left([
                'k1' => 'err1',
                'k3' => 'err2',
            ]),
            sequenceEitherAcc([
                'k1' => fn() => Either::left('err1'),
                'k2' => fn() => Either::right('val'),
                'k3' => fn() => Either::left('err2'),
            ]),
        );
    }
}
