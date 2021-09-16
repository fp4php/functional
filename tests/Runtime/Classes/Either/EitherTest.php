<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Either;

use Exception;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use PHPUnit\Framework\TestCase;

final class EitherTest extends TestCase
{
    public function testCreation(): void
    {
        $this->assertInstanceOf(Right::class, Either::right(1));
        $this->assertEquals(1, Either::right(1)->get());
        $this->assertInstanceOf(Left::class, Either::left('err'));
        $this->assertEquals('err', Either::left('err')->get());
    }

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

    public function testIsMethods(): void
    {
        $this->assertFalse(Either::right(1)->isLeft());
        $this->assertTrue(Either::right(1)->isRight());
    }

    public function testTry(): void
    {
        $this->assertInstanceOf(Right::class, Either::try(fn() => 1));
        $this->assertEquals(1, Either::try(fn() => 1)->get());

        $this->assertInstanceOf(Left::class, Either::try(fn() => throw new Exception()));
        $this->assertInstanceOf(Exception::class, Either::try(fn() => throw new Exception())->get());
    }

    public function testFold(): void
    {
        $foldRight = Either::right(1)->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $foldLeft = Either::left('err')->fold(
            fn(int $some) => $some + 1,
            fn() => 0,
        );

        $this->assertEquals(2, $foldRight);
        $this->assertEquals(0, $foldLeft);
    }

    public function testGetOrElse(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrElse(0));
        $this->assertEquals(0, Either::left('err')->getOrElse(0));
        $this->assertEquals(1, Either::right(1)->getOrCall(fn() => 0));
        $this->assertEquals(0, Either::left('err')->getOrCall(fn() => 0));
    }

    public function testOrElse(): void
    {
        $this->assertEquals(
            1,
            Either::right(1)->orElse(fn() => Either::right(2))->get()
        );

        $this->assertEquals(
            2,
            Either::left('err')->orElse(fn() => Either::right(2))->get()
        );
    }

    public function testCond(): void
    {
        $this->assertEquals(
            1,
            Either::cond(true, 1, 'err')->get()
        );

        $this->assertEquals(
            'err',
            Either::cond(false, 1, 'err')->get()
        );

        $this->assertEquals(
            1,
            Either::condLazy(true, fn() => 1, fn() => 'err')->get()
        );

        $this->assertEquals(
            'err',
            Either::condLazy(false, fn() => 1, fn() => 'err')->get()
        );
    }

    public function testToValidated(): void
    {
        $this->assertEquals(
            [1],
            Either::right([1])->toValidated()->get(),
        );

        $this->assertEquals(
            ['err'],
            Either::left(['err'])->toValidated()->get(),
        );

        $this->assertInstanceOf(
            Valid::class,
            Either::right([1])->toValidated(),
        );

        $this->assertInstanceOf(
            Invalid::class,
            Either::left(['err'])->toValidated(),
        );
    }

    public function testToOption(): void
    {
        $this->assertEquals(1, Either::right(1)->toOption()->get());
        $this->assertNull(Either::left(1)->toOption()->get());
    }
}
