<?php

declare(strict_types=1);

namespace Tests\Runtime\Monads\Either;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use PHPUnit\Framework\TestCase;

final class EitherTest extends TestCase
{
    public function testMap(): void
    {
        $right = Right::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $left = Left::of(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(1, $left->get());
    }

    public function testFlatMap(): void
    {
        $getRight = function(): Either {
            /** @psalm-var Either<string, int> $e */
            $e = Either::right(1);

            return $e;
        };

        $getLeft = function(int $r): Either {
            /** @psalm-var Either<string, int> $e */
            $e = Either::left('error');

            return $e;
        };

        $right = $getRight()
            ->flatMap(fn(int $r) => Right::of($r + 1))
            ->flatMap(fn(int $r) => Right::of($r + 1));

        $left = $getRight()
            ->flatMap(fn(int $r) => Right::of($r + 1))
            ->flatMap(function(int $r) {
                /** @psalm-var Either<string, int> $e */
                $e = Either::left('error');

                return $e;
            })
            ->flatMap(fn(int $r) => Either::right($r + 1));

        $this->assertEquals(3, $right->get());
        $this->assertEquals('error', $left->get());
    }

    public function testMapLeft(): void
    {
        /** @psalm-var Either<string, int> $either1 */
        $either1 = Either::right(1);

        /** @psalm-var Either<string, int> $either2 */
        $either2 = Either::left('error');

        $right = $either1
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->map(fn(int $r) => $r + 1);

        $left = $either2
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->mapLeft(fn(int $l) => $l + 9)
            ->map(fn(int $r) => $r + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(10, $left->get());
    }
}
